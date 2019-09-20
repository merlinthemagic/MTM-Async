# Parallel Threads:

Wrapper for this project: https://github.com/krakjoe/parallel

## Why:

Paralle is a new project, documentation has not yet been written. I learn best by wrapping someone elses work and making it flow the way i see the world. That said, i am very green in using this lib and as a result my understanding may be lacking. Some of the relationships i have created between objects might not reflect the authors intent.   

## Install:

You must use >=PHP 7.3 with ZTS from build. If you just wanna test, i have included RPMs for CentOS7 in the "Prebuilds" folder. Otherwise, you can checkout from remi.

To use MTM and Parallel add the following to your script:

```
require_once("/some/path/to/MTM/Async/Enable.php");

```

You will need to create a bootstrap file for kick starting a thread.
Most likely the bootstrap file will need to autoload your classes and it MUST include the "require_once" statement above.


## Using the Parallel wrapper:

### Start a thread:


```
$myBootStrapFile	= "/some/path/to/your/bootstrap/file.php"; //string. Path to your boot strap file
$entryClass		= "\my\class\path\OR\an\object"; //string or object
$entryMethod		= "myMethodName"; //string
$entryArgs		= array("arg1", "arg2"); //array

$threadObj 		= \MTM\Async\Factories::getThreading()->getParallel()->getNewThread($myBootStrapFile);
$threadObj->initByClassMethod($entryClass, $entryMethod, $entryArgs);
			
```

Explanation:

The above example will kick off a new thread.

1) Load your bootstrap file.

2) Instanciate an object of class: "\my\class\path\OR\an\object"

3) Call "myMethodName" on the object - passing the arguments you supplied in "entryArgs". E.g: myMethodName($arg1, $arg2);

4) Your program is running.

5) When the thread completes you can pickup the return by calling:

```
//getValue is blocking so you can wrap in if statement to avoid blocking in main
if ($threadObj->getFuture()->isDone() === true) {
	return $threadObj->getFuture()->getValue();
} else {
	return null;
}
```

### Create a channel:

```
$name		= "myUniqueChannel"; 
$size		= -1; //-1 == grow as you like, > 0 limit size to X bytes
$chanObj	= \MTM\Async\Factories::getThreading()->getParallel()->getNewChannel($name, $size);
```

#### Send on Channel:
```
$data		= "Information to the receivers on the channel";
$chanObj->setData($data);
```

#### Receive on Channel:
```
$data		= $chanObj->getData($data); //this is blocking!
```

### Create Event loop:

```
$eventLoopObj		= \MTM\Async\Factories::getThreading()->getParallel()->getNewEventLoop();
//add a channel to receive messages from this event loop
$eventLoopObj->addTarget($chanObj1);
//add another channel to receive messages from this event loop
$eventLoopObj->addTarget($chanObj2);
```

#### Send data to receivers on eventloop:

```
$data		= "Information to all receivers on the event loop";
$eventLoopObj->sendInput($data);
```

#### Receive data from event loop (always the same for a channel):

```
$data		= $chanObj->getData($data); //this is blocking!
```

##### Notes:

There is a channel dedicated to management for each Thread, you can use it to communicate with each thread directly.
This is not a Parallel requirement, but it allows direct communication to the thread if needed, I use it to pass management data to workers outside the event loops.

Main sending to Thread:

```
$data		= "Information i need to give thread";
$chanObj	= $threadObj->getChannel();
$chanObj->setData($data);
```

Thread receiving from Main:

```
$chanObj	= \MTM\Async\Factories::getThreading()->getParallel()->getTreadCtrl();
$data		= $chanObj->getData(); //this is blocking!
```
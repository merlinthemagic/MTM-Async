# Parallel Threads:

Wrapper for this project: https://github.com/krakjoe/parallel

## What is this?:

Paralle is a new project, documentation has not yet been written. I learn best by wrapping someone elses work and making it flow the way i see the world. That said, i am very green in using this lib and as a result my understanding may be lacking. Some of the relationships i have created between objects might not reflect the authors intent.   

## Install:

You must use >=PHP 7.3 with ZTS from build. If you just wanna test, i have included RPMs for CentOS7 in the "Prebuilds" folder. Otherwise, you can checkout from remi.

To use MTM-Async and Parallel:

```
composer require merlinthemagic/mtm-async

```

### Example:

```
//look at the source of Main and Thread classes.
$mainObj	= new \MTM\Async\Docs\Examples\Parallel\Simple\Main();
		
$mainObj->successThread(); //echos array of result from thread
$mainObj->errorThread(); //echos exception message thrown from thread


```

## Using the Parallel wrapper:

### Start a thread:

#### Main tread code:

You will need to create a bootstrap file for kick starting a new thread.
The bootstrap file will need to autoload your classes and it MUST include merlinthemagic/mtm-async and its dependency

```
$myBootStrapFile	= "/some/path/to/your/bootstrap/file.php"; //string. Path to your boot strap file
$entryClass		= "\my\class\path\OR\an\object"; //string or object (instance of the class)
$entryMethod		= "myMethodName"; //string
$entryArgs		= array("arg1", "arg2"); //array

$threadObj 		= \MTM\Async\Factories::getThreading()->getParallel()->getNewThread($myBootStrapFile);
$threadObj->initByClassMethod($entryClass, $entryMethod, $entryArgs);
			
```

#### Explanation:

The above example will kick off a new thread.

1) Load your bootstrap file.

2) Instanciate an object of class: "\my\class\path\OR\an\object"

3) Call "myMethodName" on the object - passing the arguments you supplied in "entryArgs". E.g: myMethodName($arg1, $arg2);

4) Your program is running.


### Get return from a thread:

```
//getValue is blocking so you can wrap in if statement to avoid blocking in main
if ($threadObj->getFuture()->isDone() === true) {
	$result = $threadObj->getFuture()->getValue();
}
```

### Create a channel:

```
$name		= "myUniqueChannelName"; 
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
$data		= $chanObj->getData(); //this is blocking!
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
$eventLoopObj->sendData($data);
```

#### Receive data from event loop (always the same for a channel):

```
$data		= $chanObj->getData(); //this is blocking!
```

##### Notes:

There is a channel dedicated to management for each Thread, you can use it to communicate with each thread directly.
This is not a Parallel requirement, but i found it useful in sending new jobs to a thread. 

Main sending to Thread:

```
$data		= "Information about a new job i want to give directly to a specific thread";
$chanObj	= $threadObj->getChannel();
$chanObj->setData($data);
```

Thread receiving from Main:

```
$chanObj	= \MTM\Async\Factories::getThreading()->getParallel()->getTreadCtrl();
$data		= $chanObj->getData(); //this is blocking!
```
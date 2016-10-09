# legionth/duplex-stream-converter

Use this project to convert a duplex stream (`ReadableStreamInterface` and `WritableStreamInterface`) to a readable stream.

**Table of Contents**
* [Usage](#usage)
 * [ReadableConverter](#readableconverter)
* [Install](#install)
* [License](#license)

## Usage

### ReadableConverter

This converter is used to convert a duplex stream into a readable stream. 
The converter uses an instance of your duplex stream. The converted stream uses the readble parts of the readable stream. So none unauthorized operations can be done on your stream, if you don't want to.

```php
// This is your own class which is a duplex stream
$duplexStream = new DuplexStream();
// This is now just a readable stream
$convertedStream = new ReadableConverter($duplexStream)
```

Use a instance of this class if you have duplex stream and you need just the readable part of the stream.

### Benchmarks

These benchmarks should show how theses wrappers work if you wrap with the `ReadableConverter` class this multiple times.

```
Wrapped 1 time:
2.1934509277344E-5 [seconds]

Wrapped 70 times:
0.00037789344787598 [seconds]
```

## Install

The recommended way to install this library is [through Composer](https://getcomposer.org).
[New to Composer?](https://getcomposer.org/doc/00-intro.md)

This will install the latest supported version:

```bash
$ composer require legionth/duplex-stream-converter:^0.1
```

## License

MIT

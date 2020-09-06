# Http Utils
Basic Utils for working with HTTP in PHP.

## Response Dumper
Dump PSR-7 Respones into the output stream.

```php
<?php

use Nyrados\Http\Utils\ResponseDumper;

$dump = new ResponseDumper($response);

// Usage:
$dump->dumpHeaders();
$dump->dumpBody();

// Or:
$dump->dump();
```

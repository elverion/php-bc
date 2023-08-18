# PHP-BC

This is a simple, minimalistic helper library for working with arbitrary-precision numbers in PHP.
It exposes a new class, `BcNumber`, that can be used to represent and do calculations on numbers
without using floats.

Floating-point numbers are considered accurate enough for general use, however, some tasks demand
_exact_ values, not "close enough." Dealing with money, for example.

# Usage

```php
use Elverion\PhpBc\Math\BcNumber;

$salesTaxPercent = '10.39';
$itemPrices = [
    '1249.99',
    '129.39',
    '275.50',
    '479.95',
];

$subTotal = new BcNumber();
foreach ($itemPrices as $price) {
    $subTotal = $subTotal->add($price);
}

$tax = $subTotal->mul($salesTaxPercent)->div('100');
$total = $subTotal->add($tax);

printf("Subtotal: $%s\nTax: $%s\nTotal: $%s\nAmount payable: $%s", $subTotal, $tax, $total, $total->ceil(2));
```

Output:

```text
Subtotal: $2134.83
Tax: $221.808837
Total: $2356.638837
Amount payable: $2356.64
```

# Why use this instead of floats?

As previously alluded to, floating-point math can be inaccurate. Consider the following:

```php
$a = 0.1;
$b = 0.2;
$c = 0.3;
print "Float equiv check:\n";
print "A + B = $c\n";
print "$c == 0.3 ? " . (($a + $b) == $c ? 'True' : 'False');
print "\n\n";
 

$a = new BcNumber('0.1');
$b = new BcNumber('0.2');
$c = new BcNumber('0.3');

print "BC equiv check:\n";
print "A + B = $c\n";
print "$c == 0.3 ? " . ($a->add($b)->equals($c) ? 'True' : 'False');
print "\n";
```

Output is:

```text
Float equiv check:
A + B = 0.3
0.3 == 0.3 ? False

BC equiv check:
A + B = 0.3
0.3 == 0.3 ? True
```

In floats, the mantissa can cause some unexpected behavior with equivilency checks.
Logically, we expect `0.1 + 0.2 == 0.3`. With floats, the `0.3` would be represented as `0.29999999999999998890` which is not exactly equal.

These issues are not limited to only equivilency checks, either:

```php
$value = 0.0;
for ($i = 0; $i < 1_000_000; $i++) {
    $value += 0.1;
}
print "Float version: $value\n";


$value = new BcNumber(0.0);
for ($i = 0; $i < 1_000_000; $i++) {
    $value = $value->add('0.1');
}
print "BC version: $value\n";
```

Output:

```text
Float version: 100000.00000133
BC version: 100000
```

# Best practices

- Values passed as input to `BcNumber` constructor or methods **should** be passed as **strings** rather than floats. While a float is a valid input, this _could_ lead to floating-point inaccuracies from prior to the data being converted to a `BcNumber`.
- Calculations this way are inherently slower, especially when run in tight loops, so do not use `BcNumber` when the accuracy isn't required but speed is.

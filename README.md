# Amazon product lookup

From ASIN code gets UPC and MPN code.

## Install

```
php composer.phar install
```

## Usage

Create the ASIN code list in CSV (`data/asin.csv`).

```
B01BH83OOM
B00LWHU9D8
...
```

Run the command.

```
php lookup.php --access-key={AWS_ACCESS_KEY} --secret-key={AWS_SECRET_KEY} [--country={AMAZON_COUNTRY_CODE}]
```

Result will be exported to CSV (`data/result.csv`).

```
{ASIN},{Title},{UPC},{MPN}
...
```

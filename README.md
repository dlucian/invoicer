# Invoicer

Invoicing API + Bootstrap Client

## Description

Invoicing tool designed to create invoices for customers world-wide, using a foreign, widely-used currency such as USD, all while taking into account the bureaucracy in the country of residence (domestic), with its local currency.

This invoicing tool is most useful when the local currency in the country of residence is different from the currency used to sell products or services. It solves this issue by creating duplicate invoices, one copy for the customer, in the foreign currency and language, and one copy for the emitting company, in the local currency and language, which also includes details of the customer's copy (such as the value in the foreign currency).

## Features

* RESTful API for automatic invoicing
* Client web application that works with the API for manual invoicing
* Create invoices in two currencies and languages (foreign and domestic)
* Foreign invoice copy in english (or any other language), using foreign currency such as USD
* Domestic invoice copy in romanian (or any other language), usign domestic currency such as RON
* Invoice number prefix and auto-numbering
* Branding watermark
* Exchange rate integration for USD to RON

## Invoicer API

### Authentication

This invoicing tool is meant as an inside tool, so current authentication implementation is a basic API key check.

Edit the `.env` file and set your API key to something long and unique (a 16 character alphanumeric string should do).

For each API call, send the `key` parameter containing the API key you've just set.

If you haven't set the API key in your configuration file or if the `key` parameter doesn't match the one you've set, all requests to the API will get a `401 Unauthorised` response. Currently, there's no plan in implementing any other sercurity measures such as oAuth or two-factor authentication. Feel free to fork and implement whatever you find necessary in your case.

### Requests

All request follow the standards of RESTful API.

### Responses

Responses follow the [jSend specifications](http://labs.omniti.com/labs/jsend) by Omniti Labs. All JSON responses include a `status` field that can be `success`, `fail` or `error`. The difference between `fail` and `error` is that while `fail` indicates an error in the request (such as invalid information provided), `error` will indicate unhandled exception and other server errors that aren't tightly related to the request. 

### Settings

The settings are stored in the `settings` table. Update these to your own needs and requirements. 

* **`next_invoice`** *(numeric)* The next invoice number that will be created via the Create invoice request.
* **`invoice_prepend`** *(string)* String to be prepended to each invoice number.
* **`invoice_digits`** *(string)* Total number of digits for the invoice number (zero padding).
* **`seller_name`** *(string)* The seller's name (company who sells goods or services).
* **`seller_info`** *(text)* Seller information such as registration number, address, bank account, social capital etc.
* **`issuer`** *(string)* Name of the person that issues the invoices (usually someone from your company).
* **`vat_percent`** *(string)* Standard VAT percent (although please take into consideration that EU companies should bill VAT percent according to the buyers country of residence, not sellers).
* **`decimals`** *(numeric)* Number of decimals for pricing values.
* **`domestic_currency`** *(string)* Usually `RON` or the currency code in the seller's country.
* **`domestic_language`** *(string)* Lowercase two-letter language code for the domestic invoice copy (ex. `ro`).
* **`foreign_currency`** *(string)* Foreign currency such as `USD`
* **`foreign_language`** *(string)* Foreign invocing language such as `en`

### Currency exchange rates

By default, the Invoicer API retrieves currency rates for Romanian Leu (RON). To adapt it to your country, update the `retrieveRemote()` method in the `\Services\CurrencyConverter` class. The code is design to locally persist every exchange rate it uses to save up API requests and bandwidth. 

To get the rate for your foreign currency, launch the `my:currency` artisan command:

    vagrant@homestead:~/invoicer/api$ php artisan my:currency
    Persisting today's currency...
    1 USD = 3.9650 RON

Exchange rate retrieving is set as a scheduled command, designed to run daily at midnight. So add scheduled commands to your cron file to have this up and running on a daily basis:

    * * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1

Exchange rates are retrieved even if they haven't been persisted by the daily cron job. But to prevent dependency on a third party service, it's recommended that you have the cron job set up.

### Invoices `/invoice` Resource

#### Listing all invoices - GET `/invoice`

Retrieves a list of invoices with basic information: id, invoice number, issue date, seller and buyer information, VAT %, products, issuer and receiver information.

Parameters:

* **`created_after`** *(optional)* retrieve only invoices created after the specified date (format `YYYY-MM-DD`)
* **`created_before`** *(optional)* retrieve only invoices created before the specified date

**Example:**

    curl -X GET -H "Cache-Control: no-cache" 'http://api.invoicer.co/v1/invoice?created_after=2015-09-01&created_before=2015-09-15&key=YOUR_API_KEY'
    
Response:

```json
{
  "status": "success",
  "code": 0,
  "data": [
    {
      "id": 323,
      "invoice": "CVI F010",
      "issued_on": "2015-09-07",
      "seller_name": "Lucian Daniliuc",
      "seller_info": "321 Vadyia Street\nTimisoara\nRomania",
      "buyer_name": "Maxwell Smart",
      "buyer_info": "123 Acme Street\nVancouver\nNorth Carolina\nUSA",
      "vat_percent": "25.00",
      "products": "[{\"test\":\"product\"}]",
      "issuer_info": "",
      "receiver_info": "",
      "branding": "",
      "extra": "",
      "created_at": "2015-09-07 10:36:52",
      "updated_at": "2015-09-07 10:36:52"
    },
    {
      "id": 324,
      "invoice": "CVI F011",
      "issued_on": "2015-09-08",
      "seller_name": "Lucian Daniliuc",
      "seller_info": "321 Wadyia Street\nTimisoara\nRomania",
      "buyer_name": "Maxwell Smart",
      "buyer_info": "123 Acme Street\nVancouver\nNorth Carolina\nUSA",
      "vat_percent": "25.00",
      "products": "[{\"test\":\"product\"}]",
      "issuer_info": "",
      "receiver_info": "",
      "branding": "",
      "extra": "",
      "created_at": "2015-09-08 06:56:08",
      "updated_at": "2015-09-08 06:56:08"
    }
  ]
}
```

#### Creating a new invoice - POST `/invoice`

**Example:**

```shell
curl --request POST \
  --url http://api.invoicer.co/v1/invoice \
  --form 'seller_name=Lucian Daniliuc' \
  --form 'seller_info=321 Vadyia Street\nTimisoara\nRomania' \
  --form 'buyer_name=Maxwell Smart' \
  --form 'buyer_info=123 Acme Street\nVancouver\nNorth Carolina\nUSA' \
  --form vat_percent=25 \
  --form 'products=[{"description":"Ice Cream","quantity":2,"price":3.5,"currency":"USD"},{"description":"Peanut Butter","quantity":1,"price":15,"currency":"USD"}]' \
  --form key=YOUR_API_KEY
```

Response:

```json
{
  "status": "success",
  "code": 0,
  "data": {
    "seller_name": "Lucian Daniliuc",
    "seller_info": "321 Vadyia Street\nTimisoara\nRomania",
    "buyer_name": "Maxwell Smart",
    "buyer_info": "123 Acme Street\nVancouver\nNorth Carolina\nUSA",
    "vat_percent": "25",
    "products": "[{\"description\":\"Ice Cream\",\"quantity\":2,\"price\":3.5,\"currency\":\"USD\"},{\"description\":\"Peanut Butter\",\"quantity\":1,\"price\":15,\"currency\":\"USD\"}]",
    "invoice": "CVI F012",
    "issued_on": "2015-09-08",
    "updated_at": "2015-09-08 07:04:30",
    "created_at": "2015-09-08 07:04:30",
    "id": 325
  }
}
```

Sending a POST request without all the required information (for example without buyer information) will result in a 400 Bad Request response:

```shell
curl --request POST \
  --url http://api.invoicer.co/v1/invoice \
  --form 'seller_name=Lucian Daniliuc' \
  --form 'seller_info=321 Vadyia Street\nTimisoara\nRomania' \
  --form vat_percent=25 \
  --form 'products=[{"description":"Ice Cream","quantity":2,"price":3.5,"currency":"USD"},{"description":"Peanut Butter","quantity":1,"price":15,"currency":"USD"}]' \
  --form key=YOUR_API_KEY
```

```json
{
  "status": "fail",
  "code": 400,
  "data": {
    "buyer_name": [
      "The buyer name field is required."
    ],
    "buyer_info": [
      "The buyer info field is required."
    ]
  }
}
```
#### Getting an invoice - GET `/invoice/{id}`

```shell
curl --request GET --url "http://api.invoicer.co/v1/invoice/F017?key=YOUR_API_KEY"
```

```json
{
  "status": "success",
  "code": 0,
  "data": {
    "id": 330,
    "invoice": "F017",
    "issued_on": "2015-09-08",
    "seller_name": "Lucian Daniliuc",
    "seller_info": "321 Wadyia Street\nTimisoara\nRomania",
    "buyer_name": "Johnny Smart",
    "buyer_info": "123 Acme Street\nVancouver\nNorth Carolina\nUSA",
    "vat_percent": "25.00",
    "products": "[{\"description\":\"Ice Cream\",\"quantity\":2,\"price\":35,\"currency\":\"RON\"},{\"description\":\"Peanut Butter\",\"quantity\":1,\"price\":150,\"currency\":\"RON\"}]",
    "issuer_info": "Lucian Daniliuc\nTM499701",
    "receiver_info": "Johnny Smart\johnny@smart.com",
    "branding": "invoicer",
    "extra": "Exchange rate 1 USD = 3.7399 RON\n<br /><br />\nServices subject to the reverse charge - VAT to be accounted for by the recipient as per Article 196 of Council\nDirective 2006/112/EC",
    "created_at": "2015-09-08 12:09:54",
    "updated_at": "2015-09-08 12:09:54",
    "exchange_rate": 0
  }
}
```

If the invoice is in a foreign currency, you'll also get the domestic price converted:

```shell
curl --request GET --url "http://api.invoicer.co/v1/invoice/F016?key=YOUR_API_KEY"
```

```json
{
  "status": "success",
  "code": 0,
  "data": {
    "id": 330,
    "invoice": "F016",
    "issued_on": "2015-09-07",
    "seller_name": "Lucian Daniliuc",
    "seller_info": "321 Wadyia Street\nTimisoara\nRomania",
    "buyer_name": "Johnny Smart",
    "buyer_info": "123 Acme Street\nVancouver\nNorth Carolina\nUSA",
    "vat_percent": "25.00",
    "products": "[{\"description\":\"Ice Cream\",\"quantity\":2,\"price\":3.5,\"currency\":\"USD\",\"price_domestic\":13.88},{\"description\":\"Peanut Butter\",\"quantity\":1,\"price\":15,\"currency\":\"USD\",\"price_domestic\":59.48}]",
    "issuer_info": "Lucian Daniliuc\nTM499701",
    "receiver_info": "Johnny Smart\johnny@smart.com",
    "branding": "invoicer",
    "extra": "Exchange rate 1 USD = 3.7399 RON\n<br /><br />\nServices subject to the reverse charge - VAT to be accounted for by the recipient as per Article 196 of Council\nDirective 2006/112/EC",
    "created_at": "2015-09-08 12:09:54",
    "updated_at": "2015-09-08 12:09:54",
    "exchange_rate": "3.9650"
  }
}
```

As you can see, the response contains the `price_domestic` attribute in the `products` JSON array and you'll also notice the `exchange_rate` attribute that's now being populated. 

#### Updating invoice information - PUT `/invoice/{id}`

#### Deleting invoice - DELETE `/invoice/{id}`

### Settings `/setting` Resource

#### Getting all settings - GET `/setting`

#### Getting a setting - GET `/setting/{name}`

#### Saving a setting - PUT `/setting/{name}`

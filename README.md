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

### Invoices `/invoice` Resource

#### Listing all invoices - GET `/invoice`

Retrieves a list of invoices with basic information: id, invoice number, issue date, seller and buyer information, VAT %, products, issuer and receiver information.

Parameters:

* **`created_after`** *(optional)* retrieve only invoices created after the specified date/time (MySQL format `YYYY-MM-DD HH:MM:SS`)
* **`created_before`** *(optional)* retrieve only invoices created before the specified date/time

**Example:**

    curl -X GET -H "Cache-Control: no-cache" 'http://api.invoicer.co/v1/invoice?created_after=2015-08-01&created_before=2015-08-30&key=YOUR_API_KEY'
    
Response:

```json
    [
      {
        "id": 1,
        "invoice": "AU5 X00196",
        "issued_on": "2015-08-10",
        "seller_name": "Hzscsr4h90 Dbqi8dmdgf",
        "seller_info": "J26/6414/1992\nRO93791398\nSPZnbIk1tXwl9tN7\nzLHjiabgizWcyi6w\nWadiya\noNi08E1o6VgVAGYp",
        "buyer_name": "Wqrss7ygmh Jmrzuprodj",
        "buyer_info": "J16/8428/2001\nRO92210334\nLVPKWkng5ttrWe8y\nOBNrlvRRE5PySbsT\nWadiya\n5BnHUFQpNkSe5pHD",
        "vat_percent": "13.00",
        "products": "{\"test\":\"info\"}",
        "issuer_info": "Ngy1vfzcmw Qzyhmuf9vb\nInWkW6CH0sLbrysU",
        "receiver_info": "Fwoblqobpk Mggvzhn8yu\ncZOzaiwLEFhlvgZu",
        "branding": "InvoicerDWL",
        "extra": "YOZl1hzdlAJm3TzE",
        "created_at": "2015-08-17 18:46:24",
        "updated_at": "-0001-11-30 00:00:00"
      },
      {
        "id": 2,
        "invoice": "YMV X00991",
        "issued_on": "2015-06-27",
        "seller_name": "Hzscsr4h90 Dbqi8dmdgf",
        "seller_info": "J26/6414/1992\nRO93791398\nSPZnbIk1tXwl9tN7\nzLHjiabgizWcyi6w\nWadiya\noNi08E1o6VgVAGYp",
        "buyer_name": "Y6sgxzg95x 44ausnkhmu",
        "buyer_info": "J72/7151/2001\nRO61357862\njeS4KMZrP6SiN0ut\nxjY856yCUumUdBJu\nWadiya\nCk7Men891ZBIctiF",
        "vat_percent": "21.00",
        "products": "{\"test\":\"info\"}",
        "issuer_info": "Ngy1vfzcmw Qzyhmuf9vb\nInWkW6CH0sLbrysU",
        "receiver_info": "Idp25d6c86 Nkuezhgp3h\nyCYiUsNzrEhvV0ac",
        "branding": "InvoicerRPD",
        "extra": "Gt3g54Vy0YFLNgiV",
        "created_at": "2015-08-17 18:46:24",
        "updated_at": "-0001-11-30 00:00:00"
      }
    ]
```

#### Creating a new invoice - POST `/invoice`

#### Getting an invoice - GET `/invoice/{id}`

#### Updating invoice information - PUT `/invoice/{id}`

#### Deleting invoice - DELETE `/invoice/{id}`

### Settings `/setting` Resource

#### Getting all settings - GET `/setting`

#### Getting a setting - GET `/setting/{name}`

#### Saving a setting - PUT `/setting/{name}`

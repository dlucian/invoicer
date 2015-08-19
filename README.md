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

#### Creating a new invoice - POST `/invoice`

#### Getting an invoice - GET `/invoice/{id}`

#### Updating invoice information - PUT `/invoice/{id}`

#### Deleting invoice - DELETE `/invoice/{id}`

### Settings `/setting` Resource

#### Getting all settings - GET `/setting`

#### Getting a setting - GET `/setting/{name}`

#### Saving a setting - PUT `/setting/{name}`

# Nagad Sandbox Payment Gateway Integration Guide

## Table of Contents

- [Nagad Sandbox Payment Gateway Integration Guide](#nagad-sandbox-payment-gateway-integration-guide)
  - [Table of Contents](#table-of-contents)
  - [1. Overview](#1-overview)
  - [2. Credentials](#2-credentials)
  - [3. Payment Initialization](#3-payment-initialization)
    - [3.1 API Request Initialization](#31-api-request-initialization)
    - [3.2 API Request Header](#32-api-request-header)
    - [3.3 API Request Body](#33-api-request-body)
    - [3.4 API Request Example](#34-api-request-example)
    - [3.5 Handling Initialization Response](#35-handling-initialization-response)
  - [4. Payment Completion](#4-payment-completion)
    - [4.1 API Request Completion](#41-api-request-completion)
    - [4.2 API Request Header](#42-api-request-header)
    - [4.3 Merchant Callback URL Parameter](#43-merchant-callback-url-parameter)
      - [How it Works](#how-it-works)
      - [Required Parameter](#required-parameter)
    - [4.4 API Request Body](#44-api-request-body)
    - [4.5 API Request Example](#45-api-request-example)
    - [4.6 Handling Completion Response](#46-handling-completion-response)
  - [Appendix](#appendix)
    - [5.1 Encryption \& Signing Methods](#51-encryption--signing-methods)
    - [5.2 Common Errors \& Debugging](#52-common-errors--debugging)
    - [5.3 Detailed Documentation](#53-detailed-documentation)

---

## 1. Overview

This guide outlines the steps for integrating the Nagad Sandbox Payment Gateway into your application. It includes details on API requests, required headers, and response handling.

## 2. Credentials

You will need the following credentials provided by Nagad to start integrating the payment gateway:

- **Merchant ID**: `Specific Merchant id`
- **Merchant Private Key**: `Merchant Private Key`
- **Payment Gateway Public Key**: `Nagad Payment Gateway Public Key`

## 3. Payment Initialization

### 3.1 API Request Initialization

To begin the payment process, initialize the payment by sending a POST request to the following URL:

**Endpoint:**

```text
http://sandbox.mynagad.com:10080/remote-payment-gateway-1.0/api/dfs/checkout/initialize/{merchantId}/{orderId}
```

**HTTP Method**
`POST`

### 3.2 API Request Header

Include the following headers in your API request:

```yaml
Content-Type: application/json
X-KM-Api-Version: v-0.2.0
X-KM-IP-V4: YOUR_APPLICATION_IP
X-KM-MC-Id: YOUR_MERCHANT_ID
X-KM-Client-Type: PC_WEB
```

### 3.3 API Request Body

The request body must be constructed in the following steps:

**Step 1 : Create JSON Payload**

```json
{
    "merchantId": "683002007104225",
    "orderId": "Order20240202124953",
    "datetime": "20240202124953",
    "challenge": 348027480
}
```

**Step 2: Encrypt the Payload**
sensitiveData = Encrypt(plainSensitiveData, NPG Public Key, PKCS1Padding)

```text

sensitiveData = "PMxT0xqUBzUrrTmoW1bkxDfcWN6lnw8l2gnCqGPL8OZ0/MLMDBS1bFXwwG+o305XLCGohgSnhSFPA8xD0xGzQXllbgbTbbQWRyLdldJPdpvyvtbjSEkPEoCtShPvpo7oqubX105SgJ7cvxex6k8QqyJ9YSPGCjYQF1CQmJyo3ChqMO/JoWtSyc1KQKs8knKP1uzJttKF30rNzHAP1BD9AvYoda72S1WJEAMbTc34KGyy2f462m8zsTBxFYnPFqpVOBf2BQOT2QCOPly8W39/UfEhq/RqDhvxDSubmpL8YisrxHeKnEo3Br4aweouEdBx4l276AfcAx0DtfHttBtQ/Q=="
```

**Step 3: Generate a Signature**
signature = Sign(plainSensitiveData, Merchant Private Key,
SHA1withRSA)

```text
signature = "B6QMGIFREuXwqZ/1D91rqIxMf5lCKcqwA5TATzTv5Z2OYN8e7Ex2uldjAX3N647qN2IqPUAGOV+722qGYZjfW7LWJdA6pY0buQwoZdfHqZX3zt57mXMNdEJNoBHVqEqXGXXB5Ke+U1r0kBxrocImiykHiLbAmJxvO3CR1EsFnLUhldwzRPw9WU/DL1sCh6g02mcy/z4X09CtMQbmbVdkfm3Vn3Rdy8lcPWz1tQkq/hkdMe2t/w+p6dc2hEO6wSsWwyoYOWS9X4pOYwR0ZPJfU/U+9FAcRevHoe2WdzmE7fbGjLoqs3MOgNkaWAzXxHqsw8pksGCzbVw7xUeuJxOtsw=="
```

### 3.4 API Request Example

Here is an example using curl to send the initialization request:

```bash
curl -X POST http://sandbox.mynagad.com:10080/remote-payment-gateway-1.0/api/dfs/check-out/initialize/683002007104225/Order20240202124953 \
  -H "X-KM-IP-V4: 192.168.0.1" \
  -H "X-KM-MC-Id: 683002007104225" \
  -H "X-KM-Client-Type: PC_WEB" \
  -H "X-KM-Api-Version: v-0.2.0" \
  -d '{
    "datetime": "20240202124953",
    "sensitiveData": "PMxT0xqUBzUrrTmoW1bkxDfcWN6lnw8l2gnCqGPL8OZ0/MLMDBS1bFXwwG+o305XLCGohgSnhSFPA8xD0xGzQXllbgbTbbQWRyLdldJPdpvyvtbjSEkPEoCtShPvpo7oqubX105SgJ7cvxex6k8QqyJ9YSPGCjYQF1CQmJyo3ChqMO/JoWtSyc1KQKs8knKP1uzJttKF30rNzHAP1BD9AvYoda72S1WJEAMbTc34KGyy2f462m8zsTBxFYnPFqpVOBf2BQOT2QCOPly8W39/UfEhq/RqDhvxDSubmpL8YisrxHeKnEo3Br4aweouEdBx4l276AfcAx0DtfHttBtQ/Q==",
    "signature": "B6QMGIFREuXwqZ/1D91rqIxMf5lCKcqwA5TATzTv5Z2OYN8e7Ex2uldjAX3N647qN2IqPUAGOV+722qGYZjfW7LWJdA6pY0buQwoZdfHqZX3zt57mXMNdEJNoBHVqEqXGXXB5Ke+U1r0kBxrocImiykHiLbAmJxvO3CR1EsFnLUhldwzRPw9WU/DL1sCh6g02mcy/z4X09CtMQbmbVdkfm3Vn3Rdy8lcPWz1tQkq/hkdMe2t/w+p6dc2hEO6wSsWwyoYOWS9X4pOYwR0ZPJfU/U+9FAcRevHoe2WdzmE7fbGjLoqs3MOgNkaWAzXxHqsw8pksGCzbVw7xUeuJxOtsw=="
  }'
```

### 3.5 Handling Initialization Response

After sending the request, you will receive a response containing sensitiveData and signature.

Example Response:

```json
{
  "sensitiveData": "DKEmjgIU5QODnqw1N6yRb1KCo11+4Be83ZW7bQJX3zAodO4ABn4WRFkvjsz11tYwCmx1vpFmTSgKfWEY7APVYmxoB9zT+M4QRQ1H3DGvTxr7w/EJxK4HDV4UfUTlp5lCHh/lkBaVrNDWsEKa0Qp8Kxj6TlNQ1X8UFE2T/QU8wY63XTHmWCj2bhfGGrW+5WUzpJ1ofszee3QC+WRj8/BUT+j1AbTSqv2/frLg16gh/QJYxIxHdvNu0zV7ai4KO+oCuFZXMebvwGXNac5GHlfa3OZRwihG1gphdeeF1yhphGK6uo/t7DgcUwWDJq5RMkMe7fIU59ce8KTVFxiqBoosQw==",
  "signature": "UmMg7n3g+6NGPLYbELnOoI93kZ0ux6C0xP98rM3KOBSBS75GkcMhIGSDqaX5V+wmuk6SvcRAa1+250rnpO9oaZsOkPuk5hO+gQEe7gHNUrcK2d5XwJSr6VTkRv/fJJiAzPtNNmgC1aHFPj2J+jEGDiL/38aNu9CatrC3rG3urscXwPlafp77bdnDX344cFw2CmhHOQ1jnFKxWVZvIgAYu10rwnGIUU/7SgDgCper7KSd3v9/Smlrm3aOJT6sjAcjnfMg1F+3tdoCQfXbK5am/MOCx8vGUfRk/BoOz9uxaHCC9Z32+spAhj0rRie2GaEPQjvaJkWpQwKvLA+6vC7YHA=="
}
```

**Step 1: Decrypt the Response**
Decrypt the sensitiveData using your Merchant Private Key

```text
decryptedSensitiveData = Decrypt(Base64_Decode(sensitiveData), Merchant Private Key, PKCS1Padding)
```

**Step 2: Verify the Signature**
Verify the signature using Nagad Payment Gateway's Public Key

```text
verification = Verify(decryptedSensitiveData, Base64_Decode(signature), NPG Public Key, SHA1withRSA)
```

Example Decrypted Data:

```json
{
  "paymentReferenceId": "MDIwMjEyNTQ0NTAzMy4...",
  "challenge": "cb109c6582c56f33f74f",
  "acceptDateTime": "20240202125445"
}
```

## 4. Payment Completion

### 4.1 API Request Completion

To complete the payment, send a POST request to the following URL:

**Endpoint:**

```text
http://sandbox.mynagad.com:10080/remote-payment-gateway-1.0/api/dfs/check-out/complete/{PaymentReferenceId_From_decryptedSensitiveData}
```

### 4.2 API Request Header

Use the same headers as in the initialization request.

### 4.3 Merchant Callback URL Parameter

The `merchantCallbackURL` parameter is an essential part of the payment gateway integration process. It is used to notify your application about the payment status once the transaction is completed.

#### How it Works

After a customer do a payment, the Nagad payment gateway processes the transaction and sends the result (whether it was successful, failed, or canceled) to the URL you specify as the `merchantCallbackURL`. Your backend should handle this callback to update the transaction status accordingly.

#### Required Parameter

You must provide a valid, publicly accessible URL as the `merchantCallbackURL`. The Nagad server will send a request to this URL with the payment response data in params.

### 4.4 API Request Body

Construct the request body in the following steps:
**Step 1: Create JSON Payload**

```json
{
  "merchantId": "683002007104225",
  "orderId": "Order20240202124953",
  "currencyCode": "050",
  "amount": "100",
  "challenge": "cb109c6582c56f33f74f"
}
```

**Step 2: Encrypt the Payload**
Encrypt the JSON payload using Nagad Payment Gateway's Public Key with PKCS1Padding.

```text
sensitiveData = Encrypt(plainSensitiveData, NPG Public Key, PKCS1Padding)
```

**Step 3: Generate a Signature**
Sign the payload using your Merchant Private Key with SHA1withRSA.

```text
signature = Sign(plainSensitiveData, Merchant Private Key, SHA1withRSA)
```

### 4.5 API Request Example

```bash
curl -X POST http://sandbox.mynagad.com:10080/remote-payment-gateway-1.0/api/dfs/check-out/complete/MDIwMjEyNTQ0NTAzMy42ODMwMDIwMDcxMDQyMjUuT3JkZXIyMDI0MDIwMjEyNDk1My5jYjEwOWM2NTgyYzU2ZjMzZjc0Zg== \
  -H "X-KM-IP-V4: 192.168.0.1" \
  -H "X-KM-MC-Id: 683002007104225" \
  -H "X-KM-Client-Type: PC_WEB" \
  -H "X-KM-Api-Version: v-0.2.0" \
  -d '{
  "sensitiveData": "LCTqbe3kvVotumG93skO6+iPhzkK0cr38ZnyZCcls93gx2bIiriiIA9S9T3Kf5Q6sbxTi2tx0Kx5BI16dARxQw52sOnlHLiGOHL9Sc1SPpj7WMSrFfL/N5kk9MJQ2iUxSUpRceNDKc/PniuoVro+Jpey3Y+Y0Wx1TwolkmAuSjaONMJf+WVmtoUvS7LFwkm4Mbu74PMsrxM+i80yMmumcuCNKerTp4UhZ9hXwD2sbRGevfmcavjDwShlK4+IatZLNQe9uxn9MZ6RfFH3M0vEqcYywd/qfKye0OLE3PlslOZRK2JALWJDdr4ItvllBfIzSMTmnQ97hT2/uLKQk2jSTA==",
  "signature": "DZz8IslvhHkEpesk4BQLaQdZ32fGv+HrULA7HLixku/uLlk3wx3xrDhKZ5142nHziX4I9G4fGF7sS8qGwl9I85GkVIS4bznBHhcP2wCifK5wT4pJ5HX1rn0veA+7OrZ89Y/61kMk+wFeX0s/88HbZwusE1zbMOPG8AwtG7UDX1QOyxXfH9ucNLQEFZ9S1ouR9WDsiWZN+GLep5oYLFiLDjOoSR4QSdNTTtJaJXz/ymr8JW3bXKmUlTPNiGY7ko/R/i55V5T0/HULXdtw2vDRkDslwfsfBKOAO2uDiL88NxzADdoZFtAWBeI9XNZOnKdAhKjI1NKXFrzbEsxQncqDTQ==",
  "merchantCallbackURL": "http://sandbox.mynagad.com:10707/merchant-server/web/confirm",
  "additionalMerchantInfo": {
    "productName": "shirt",
    "productCount": 1
  }
}'
```

### 4.6 Handling Completion Response

After sending the completion request, you will receive a callBackUrl which redirects to the Nagad payment gateway page.

Example Response:

```json
{
  "callBackUrl": "https://sandbox-ssl.mynagad.com:10061/check-out/...",
  "status": "Success"
}
```

The callBackUrl will display the payment gateway page for the user to complete the payment.

## Appendix

### 5.1 Encryption & Signing Methods

- **Encryption**: Use PKCS1Padding with Nagad Payment Gateway's Public Key.
- **Signing**: Use SHA1withRSA with your Merchant Private Key.

### 5.2 Common Errors & Debugging

- **Ensure Correct Keys**: Ensure that the keys are correctly taken from the mail in sandbox and correctly generated for live.
- **Invalid Signature**: Ensure that the signature is correctly generated using the specified algorithm and keys.
- **Decryption Issues**: Verify that the correct key and padding scheme are used for decryption.

### 5.3 Detailed Documentation

For more detailed documentation, you can download the official Nagad Sandbox Payment Gateway integration guide [here](https://github.com/muhibbin-munna/nagad_pg_php/blob/master/resource/Nagad%20Online%20Payment%20API%20Integration%20Guide%20v3.3.pdf). This guide contains step-by-step instructions with details to assist you throughout the integration process.

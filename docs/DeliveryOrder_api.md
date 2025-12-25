# Delivery Order API Documentation

## Base URL
```
http://your-api-domain.com/api
```

## Authentication
All endpoints require authentication using Sanctum tokens. Include the token in the Authorization header:
```
Authorization: Bearer {your-token}
```

## Endpoints

### 1. Get All Delivery Orders
**GET** `/delivery-orders`

#### Description
Retrieve a list of all delivery orders.

#### Headers
```
Authorization: Bearer {your-token}
Content-Type: application/json
```

#### Response
- **Status Code**: 200 OK
- **Response Body**:
```json
[
  {
    "id": 1,
    "code": "DO-TEST-12345",
    "date": "2025-12-31T10:00:00.000000Z",
    "customer": "Test Customer",
    "address": "123 Test Street, Test City",
    "driver_name": "John Doe",
    "vehicle_plate": "ABC-1234",
    "created_at": "2025-12-25T14:10:59.000000Z",
    "updated_at": "2025-12-25T14:10:59.000000Z"
  }
]
```

---

### 2. Get Single Delivery Order
**GET** `/delivery-orders/{id}`

#### Description
Retrieve a specific delivery order by ID.

#### Path Parameters
- `id` (integer): The ID of the delivery order to retrieve

#### Headers
```
Authorization: Bearer {your-token}
Content-Type: application/json
```

#### Response
- **Status Code**: 200 OK
- **Response Body**:
```json
{
  "id": 1,
  "code": "DO-TEST-12345",
  "date": "2025-12-31T10:00:00.000000Z",
  "customer": "Test Customer",
  "address": "123 Test Street, Test City",
  "driver_name": "John Doe",
  "vehicle_plate": "ABC-1234",
  "created_at": "2025-12-25T14:10:59.000000Z",
  "updated_at": "2025-12-25T14:10:59.000000Z"
}
```

#### Errors
- **Status Code**: 401 Unauthorized - If authentication fails
- **Status Code**: 404 Not Found - If the delivery order doesn't exist

---

### 3. Create Delivery Order
**POST** `/delivery-orders`

#### Description
Create a new delivery order.

#### Headers
```
Authorization: Bearer {your-token}
Content-Type: application/json
```

#### Request Body
```json
{
  "code": "DO-NEW-67890",
  "date": "2025-12-31 15:30:00",
  "customer": "New Customer",
  "address": "456 New Street, New City",
  "driver_name": "Jane Smith",
  "vehicle_plate": "XYZ-9876"
}
```

#### Request Body Parameters
- `code` (string, required): Unique code for the delivery order
- `date` (string, required): Date and time of the delivery (ISO format or datetime string)
- `customer` (string, required): Name of the customer
- `address` (string, required): Delivery address
- `driver_name` (string, required): Name of the delivery driver
- `vehicle_plate` (string, required): Vehicle license plate number

#### Response
- **Status Code**: 201 Created
- **Response Body**:
```json
{
  "id": 2,
  "code": "DO-NEW-67890",
  "date": "2025-12-31T15:30:00.000000Z",
  "customer": "New Customer",
  "address": "456 New Street, New City",
  "driver_name": "Jane Smith",
  "vehicle_plate": "XYZ-9876",
  "created_at": "2025-12-25T14:10:59.000000Z",
  "updated_at": "2025-12-25T14:10:59.000000Z"
}
```

#### Errors
- **Status Code**: 401 Unauthorized - If authentication fails
- **Status Code**: 422 Unprocessable Entity - If validation fails
  - `code` must be unique
  - All required fields must be provided

---

### 4. Update Delivery Order
**PUT** `/delivery-orders/{id}`

#### Description
Update an existing delivery order with all fields.

#### Path Parameters
- `id` (integer): The ID of the delivery order to update

#### Headers
```
Authorization: Bearer {your-token}
Content-Type: application/json
```

#### Request Body
```json
{
  "code": "DO-UPDATED-54321",
  "date": "2025-12-31 16:00:00",
  "customer": "Updated Customer",
  "address": "789 Updated Street, Updated City",
  "driver_name": "John Updated",
  "vehicle_plate": "DEF-5555"
}
```

#### Request Body Parameters
- `code` (string): Unique code for the delivery order
- `date` (string): Date and time of the delivery (ISO format or datetime string)
- `customer` (string): Name of the customer
- `address` (string): Delivery address
- `driver_name` (string): Name of the delivery driver
- `vehicle_plate` (string): Vehicle license plate number

#### Response
- **Status Code**: 200 OK
- **Response Body**:
```json
{
  "id": 1,
  "code": "DO-UPDATED-54321",
  "date": "2025-12-31T16:00:00.000000Z",
  "customer": "Updated Customer",
  "address": "789 Updated Street, Updated City",
  "driver_name": "John Updated",
  "vehicle_plate": "DEF-5555",
  "created_at": "2025-12-25T14:10:59.000000Z",
  "updated_at": "2025-12-25T14:11:00.000000Z"
}
```

#### Errors
- **Status Code**: 401 Unauthorized - If authentication fails
- **Status Code**: 404 Not Found - If the delivery order doesn't exist
- **Status Code**: 422 Unprocessable Entity - If validation fails

---

### 5. Partially Update Delivery Order
**PATCH** `/delivery-orders/{id}`

#### Description
Partially update an existing delivery order.

#### Path Parameters
- `id` (integer): The ID of the delivery order to update

#### Headers
```
Authorization: Bearer {your-token}
Content-Type: application/json
```

#### Request Body (example - only updating the driver name)
```json
{
  "driver_name": "New Driver Name"
}
```

#### Request Body Parameters
- `code` (string, optional): Unique code for the delivery order
- `date` (string, optional): Date and time of the delivery (ISO format or datetime string)
- `customer` (string, optional): Name of the customer
- `address` (string, optional): Delivery address
- `driver_name` (string, optional): Name of the delivery driver
- `vehicle_plate` (string, optional): Vehicle license plate number

#### Response
- **Status Code**: 200 OK
- **Response Body**:
```json
{
  "id": 1,
  "code": "DO-EXISTING-12345",
  "date": "2025-12-31T10:00:00.000000Z",
  "customer": "Existing Customer",
  "address": "123 Existing Street, Existing City",
  "driver_name": "New Driver Name",
  "vehicle_plate": "ABC-1234",
  "created_at": "2025-12-25T14:10:59.000000Z",
  "updated_at": "2025-12-25T14:12:00.000000Z"
}
```

#### Errors
- **Status Code**: 401 Unauthorized - If authentication fails
- **Status Code**: 404 Not Found - If the delivery order doesn't exist
- **Status Code**: 422 Unprocessable Entity - If validation fails

---

### 6. Delete Delivery Order
**DELETE** `/delivery-orders/{id}`

#### Description
Delete a specific delivery order by ID.

#### Path Parameters
- `id` (integer): The ID of the delivery order to delete

#### Headers
```
Authorization: Bearer {your-token}
Content-Type: application/json
```

#### Response
- **Status Code**: 204 No Content
- **Response Body**: Empty

#### Errors
- **Status Code**: 401 Unauthorized - If authentication fails
- **Status Code**: 404 Not Found - If the delivery order doesn't exist

---

## Error Response Format

When an error occurs, the API returns a JSON response in the following format:

```json
{
  "message": "Error message",
  "errors": {
    "field_name": [
      "Validation error message"
    ]
  }
}
```

## Common Error Status Codes
- **401 Unauthorized**: Authentication required or invalid token
- **404 Not Found**: Resource does not exist
- **422 Unprocessable Entity**: Validation errors
- **500 Internal Server Error**: Server error
# Oro\Bundle\OrganizationBundle\Api\Model\Contact

## ACTIONS

### create

Validates the customer user email and password, and if the credentials are valid, returns the API access key
that can be used for subsequent API requests.

{@request:json_api}
Example of the request:

```JSON
{
  "meta": {
    "lstPhone": "1111,2222"
  }
}
```

{@/request}

## FIELDS

**The required field.**

### lstPhone

List phone number


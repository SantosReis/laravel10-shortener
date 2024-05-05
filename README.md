## URL Shortener

Backend URL Shortener to integrate with frontend following frontend: [vue-url-shortener](https://github.com/SantosReis/vue-url-shortener).

NOTE: SQLite is enabled to use.

## Features

-   REST API Design
-   Generate url shortener
-   Redirect shortener to original ones
-   Database storage

### Testing features available

NOTE: there are 2 of 5 tests deployed

```
php artisan test --filter=UrlShortenerTest
```

### API endpoints available

<details>
 <summary><code>POST</code> <code><b>/api/shortener</b></code> <code>Generate URL Shortener</code></summary>

##### Parameters

> | name  | type     | data type    | description             |
> | ----- | -------- | ------------ | ----------------------- |
> | `url` | required | string (255) | The url to be shortened |

##### Responses

> | http code | content-type       | response    |
> | --------- | ------------------ | ----------- |
> | `200`     | `application/json` | JSON string |

##### Example cURL

> ```javascript
>  curl -X GET -H "Content-Type: application/json" http://localhost:8889/api/shortener
> ```

</details>

<details>
 <summary><code>GET</code> <code><b>/api/shortener-list</b></code> <code>Listing URL Shorteners</code></summary>

##### Parameters

> None

##### Responses

> | http code | content-type               | response    |
> | --------- | -------------------------- | ----------- |
> | `200`     | `text/plain;charset=UTF-8` | JSON string |

##### Example cURL

> ```javascript
>  curl -X GET -H "Content-Type: application/json" http://localhost:8889/api/shortener-list
> ```

</details>

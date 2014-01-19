### Changelog

### 1.0.0
- Initial release of indatus/active-resource evolution
- **Significant updates with non-backwards-compatible changes for ActiveResource**
- Refactored many static methods and properties to make better use of the _Facade Pattern_
- Refactored service provider to bind package and dependent core classes
- Added Config settings management
- Added unit tests
- Added dependencies on `illuminate/config ~4` and `iluminate/container ~4`
- Added dependency on forked `illuminate/filesystem` [found here](https://github.com/brianwebb01/filesystem)
- Renamed `ActiveResource::inflateFromArray()` to `Trucker::fill()`
- Removed `$instance->updateAttributes()`
- Replaced `$instance->attributes` with `$instance->attributes()`
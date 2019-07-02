# CI4DebugTool
Debug tool for Code Igniter 4

## Usage
Add a debug message
`Data::debug("Some debug message");`
Almost everything can be thrown to `Data::debug`; simple types, arrays, objects, Entities. 

Or set a variable
`Data::set("name", $someThing);`

Variable can be a simple type or an `Entity` of `OrmExtension`. `Data` will use `toArray` or `allToArray` on `Entity` if supplied by `OrmExtension`.

Use `Data::getStore()` to get the data and present it. Ex. In your base controller add this in the constructor
```php 
Data::set('bench', 0);
timer('code-start');
```
Create a method to print response. Ex 
```php 
protected function success($code = 200) {
    $this->response->setStatusCode($code);
    Data::set('bench', timer()->getElapsedTime('code-start'));
    $this->response->setJSON(Data::getStore());
    $this->response->send();
}
```
This will give you a nice benchmark on every json response. 

## Live Template
For fast debugging, add this Live Template to your IDE: `Data::debug(get_class($this), "$END$");`.

## Code Igniter 4 Error page
To see the debug data in Code Igniter 4 Error page follow these steps
### 1
Edit `app/Views/errors/html/error_exception.php` and add a tab for Data in the list with id `#tabs`.
```html
<li><a href="#data">Data</a></li>
```
### 2 
In the same file add this section under the div with class `.tab-content`.
```html
<!-- Data -->
<div class="content" id="data">
    <pre><code><?=json_encode(\DebugTool\Data::getStore(), JSON_PRETTY_PRINT)?></code></pre>
</div>
```

### 3 Optional
To top is all up. Go to `app/Views/errors/debug.js` and add this at the bottom of `init()`-function
```javascript
// Show last selected tab
if(window.localStorage.getItem('lastTab')) {
    tabLinks[window.localStorage.getItem('lastTab')].click();
}
```
And add this to `showTab()`-function
```javascript
window.localStorage.setItem("lastTab", selectedId);
```
This will remember the tab between page reload.

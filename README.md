# SimplePaginator bundle for Symfony2/Doctrine2/Twig

This package contains a bundle to easily paginate complex queries efficiently and without effort.

## Dependencies

For the bundle to work properly you should also install `DoctrineExtensions\Paginate`. 

Bundle and instruccions at https://github.com/beberlei/DoctrineExtensions

## How to include SimplePaginatorBundle in your code

You should clone this repository in your Symfony's `vendor/bundles` directory, add it into `autoload.php` file:

```php
<?php
$loader->registerNamespaces(array(
  'Symfony' => array(__DIR__.'/../vendor/symfony/src', __DIR__.'/../vendor/bundles'),
  ...
  'Ideup'   => __DIR__.'/../vendor/bundles',
  );
```
... and in your `AppKernel.php` file:

```php
<?php
public function registerBundles()
{
    $bundles = array(
      ...
        new Ideup\SimplePaginatorBundle\IdeupSimplePaginatorBundle(),
      );
}
```
... so you are ready now to use IdeupSimplePaginatorBundle as a service.

Since the `Paginator::paginate()` method needs a `Query` object to work with, you need to change slightly your entity Repository classes:

  * Before

```php
<?php
class User extends EntityRepository 
{
  public function findByMyCriteria() {
    $query = $this->_em->createQuery('...');
    return $query->getResult();
  }
}
```

  * After

```php
<?php
class User extends EntityRepository 
{
  public function findByMyCriteria() {
    return $this->findByMyCriteriaDQL()->getResult();
  }

  public function findByMyCriteriaDQL() {
    $query = $this->_em->createQuery('...');
    return $query;
  }
}
```

In your controller you can be able to instantiate the paginator service. `SimplePaginatorBundle` is smart enough to
detect the current page and the maximum items per page from the `Request` context, so you don't need to type more 
boilerplate code!

  * Before

```php
<?php
class MyController extends Controller
{
  public function listAction() {
    $paginator = $this->get('ideup.simple_paginator');

    $users = $paginator->paginate($em->getRepository('MyBundle:User')->findByMyCriteriaDQL())->getResult();

    $vars = array(
        'users'     => $users,
        'paginator' => $paginator);
    return $this->render('MyBundle:User:list.html.twig', $vars);
  }
}
```

Note that the variable `$users` contains only the paginated subset of the Doctrine collection and you can query
`$paginator` object to obtain information about the pagination process; such as how many items are in the full
collection, in wich page are we, wich is the last page, etc.

  * After

```php
<?php
class MyController extends Controller
{
  public function listAction() {
    $paginator = $this->get('ideup.simple_paginator');

    $users = $paginator->paginate($em->getRepository('MyBundle:User')->findByMyCriteriaDQL())->getResult();

    $vars = array(
        'users'     => $users,
    );
    return $this->render('MyBundle:User:list.html.twig', $vars);
  }
}
```
Note that now you don't need to pass `$paginator` variable to template unless you want to obtain information about
pagination process.

## How to render a paginator in your view

  * Before

```jinja
<ul id="paginate_elements">
  {% if paginator.currentPage > 1 %}
    <li><a href="{{ path('my_controller_route', {'page': paginator.previousPage}) }}">previous</a></li>
  {% else %}
    <li class="left_disabled"><a href="#">previous</a></li>
  {% endif %}

  {% for page in paginator.minPageInRange..paginator.maxPageInRange %}
    {% if page == paginator.currentPage %}
      <li><a class="current" href="#">{{ page }}</a></li>
    {% else %}
      <li><a href="{{ path('my_controller_route', {'page': page}) }}">{{ page }}</a></li>
    {% endif %}
  {% endfor %}

  {% if paginator.currentPage < paginator.lastPage %}
    <li class="right"><a href="{{ path('my_controller_route', {'page': paginator.nextPage}) }}">next</a></li>
  {% else %}
    <li class="right_disabled">next</li>
  {% endif %}
</ul>
```

  * After

```jinja
  {{ simple_paginator_render('my_controller_route') }}
```
You can to customize paginator view as follows:

```jinja
  {{ simple_paginator_render('my_controller_route', null, { params }) }}
```
where `params` may be:

- `routeParams`, the params needed by the controller route (default: `{}`)

- `container_class`, the CSS class applied to the `<ul>` element that wraps the paginator (default: `simple_paginator`)

- `firstPageText`, the text shown on the first page link (default: `first`)
- `firstEnabledClass`, the CSS class applied to the first page link if there is a first page (default: `first`)
- `firstDisabledClass`, the CSS class applied to the first page text when there is no first page (default: `first_disabled`)

- `previousPageText`, the text shown on the previous page link (default: `previous`)
- `previousEnabledClass`, the CSS class applied to the previous page link if there is a previous page (default: `left`)
- `previousDisabledClass`, the CSS class applied to the previous page text when there is no previous page (default: `left_disabled`)

- `currentClass`, the CSS class applied to the `<li>` element that wraps the current page link (default: `current`)

- `nextPageText`, the text shown on the next page link (default: `next`)
- `nextEnabledClass`, the CSS class applied to the next page link if there is a next page (default: `right`)
- `nextDisabledClass`, the CSS class applied to the next page text when there is no next page (default: `right_disabled`)

- `lastPageText`, the text shown on the last page link (default: `last`)
- `lastEnabledClass`, the CSS class applied to the last page link if there is a last page (default: `last`)
- `lastDisabledClass`, the CSS class applied to the last page text when there is no last page (default: `last_disabled`)

For example, if you want to customize paginator view to show a route that receive a 
parameter `id` and you want to change container class:

```jinja
  {{ simple_paginator_render('my_controller_route', null, { 
       'routeParams' : {'id' : id},
       'container_class' : 'custom_simple_paginator_class'
     })
  }}
```
If your needs are out of this sight you can customize it in your own view:

```jinja
  {{ simple_paginator_render('my_controller_route', null, {....}, 'MyBundle:MyViewFolder:MyViewFile.html.twig') }}
```
To create `MyBundle:MyViewFolder:MyViewFile.html.twig` copy from default template that is included inside the bundle
`Resources\views\Paginator\simple-paginator-list-view.html.twig` and customize it in your own Bundle.

For example, if you want only to show paginator numbers, your template sounds like this 

`MyBundle\Resources\views\MyViewFolder\MyViewFile.html.twig`:

```jinja
<ul class="{{ container_class }}">
    <!-- NUMBERS -->
    {% for page in minPage..maxPage %}
        {% if page == currentPage %}
            <li class="{{ currentClass }}">
                {{ page }}
            </li>
        {% else %}
            {% set rParams =  {'page': page, 'paginatorId': id} | merge(routeParams) %}
            <li>
                <a href="{{ path(route, rParams) }}">{{ page }}</a>
            </li>
        {% endif %}
    {% endfor %}
</ul>
```

## How to include more than one paginator in a single view

`SimplePaginatorBundle` supports multiple paginators, you should specify an id in your controller and view calls. Note 
that you can modify the particular properties of each paginator.

  * Before

```php
<?php
class MyController extends Controller
{
  public function listAction() {
    $paginator = $this->get('ideup.simple_paginator');

    $paginator->setItemsPerPage(25, 'users');
    $users = $paginator->paginate($em->getRepository('MyBundle:User')->findByMyCriteriaDQL(), 'users')->getResult();

    $paginator->setItemsPerPage(5, 'groups');
    $groups = $paginator->paginate($em->getRepository('MyBundle:User')->findByMyCriteriaDQL(), 'groups')->getResult();

    $vars = array(
        'users'     => $users,
        'groups'    => $groups,
        'paginator' => $paginator);
    return $this->render('MyBundle:User:list.html.twig', $vars);
  }
}

```

In the view you also need to specify the paginator id:

```jinja
<ul id="paginate_elements">
  {% if paginator.currentPage('users') > 1 %}
    <li><a href="{{ path('my_controller_route', {'page': paginator.previousPage('users'), 'paginatorId': 'users'}) }}">previous</a></li>
  {% else %}
    <li class="left_disabled"><a href="#">previous</a></li>
  {% endif %}

  {% for page in paginator.minPageInRange('users')..paginator.maxPageInRange('users') %}
    {% if page == paginator.currentPage('users') %}
      <li><a class="current" href="#">{{ page }}</a></li>
    {% else %}
      <li><a href="{{ path('my_controller_route', {'page': page, 'paginatorId': 'users'}) }}">{{ page }}</a></li>
    {% endif %}
  {% endfor %}

  {% if paginator.currentPage('users') < paginator.lastPage('users') %}
    <li class="right"><a href="{{ path('my_controller_route', {'page': paginator.nextPage('users'), 'paginatorId': 'users'}) }}">next</a></li>
  {% else %}
    <li class="right_disabled">next</li>
  {% endif %}
</ul>
```
  * After

```php
<?php
class MyController extends Controller
{
  public function listAction() {
    $paginator = $this->get('ideup.simple_paginator');

    $users = $paginator
      ->setItemsPerPage(25, 'users');
      ->paginate($em->getRepository('MyBundle:User')->findByMyCriteriaDQL(), 'users')
      ->getResult()
    ;

    // Now also we can paginate arrays
    $allGroups = array('group1', 'group2', 'group3', 'group4', 'group5');

    $groups = $paginator
      ->setItemsPerPage(3, 'groups')
      ->paginate($allGroups, 'groups')
      ->getResult()
    ;

    $vars = array(
        'users'     => $users,
        'groups'    => $groups,
    );
    return $this->render('MyBundle:User:list.html.twig', $vars);
  }
}

```

In the view you also need to specify the paginator id:

```jinja
  {{ simple_paginator_render('my_controller_route', 'users', {....}) }}
  
  {{ simple_paginator_render('my_controller_route', 'groups', {....}) }}
```

## Authors

* Francisco Javier Aceituno
* Luis Cordoval
* Moisés Maciá
* Gustavo Piltcher

## Changelog

v0.91

* Added support to paginate arrays.

* Changed setter methods. Now this methods return paginate object.

* Added twig support.

v0.9

* Added dependency to DoctrineExtensions\Paginate to handle proper pagination (see https://github.com/beberlei/DoctrineExtensions)
 
* Added support to paginate multiple lists at once
 
* Changed the Paginador class name to Paginator, this is how services.xml defines our service, with a parameter set to the class implementing pagination and passing a service id
 
```xml
<parameters>
  <parameter key="simple_paginador.class">Ideup\SimplePaginatorBundle\Paginator\Paginator</parameter>
</parameters>

<services>
  <service id="ideup.simple_paginator" class="%simple_paginator.class%">
    <argument type="service" id="request" strict="false" />
  </service>
</services>
```

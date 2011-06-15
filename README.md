# SimplePaginator bundle for Symfony2/Doctrine2

This package contains a bundle to easily paginate complex queries efficiently and without effort.

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

## How to render a paginator in your view

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

## How to include more than one paginator in a single view

`SimplePaginatorBundle` supports multiple paginators, you should specify an id in your controller and view calls. Note 
that you can modify the particular properties of each paginator.

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

## Authors

* javiacei
* cordoval
* Moisés Maciá

## Changelog

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
 
* implemented Twig extension and calling the Class Paginator from there

## TODO

* Testing it with a hello world sample
* Making it work with a more generic Doctrine Collections
* Make a twig extension to render the paginator


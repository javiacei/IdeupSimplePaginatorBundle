// src/Ideaup/TwigExtension

/*  
 * Luis Cordova cordoval@gmail.com
 * Symfony2 Developer
 */

namespace  Ideaup\SimplePaginatorBundle\TwigExtension;

use Ideaup\SimplePaginatorBundle\Paginator\Paginator;

class MyTwigExtension extends \Twig_Extension
{
    public function getFilters ()
    {
        return array(
            'paginator'  =>  new \Twig_Filter_Method($this, 'paginate')
        );
    }

    public function paginate ($request, &$query)
    {
        $paginator = new Paginator ($request);
        $samequery = $paginator->transformQuery(&$query);
        $strPaginator = $paginator->render();
        
        return $strPaginator;
    }

    public function getName()
    {
        return 'my_twig_extension';
    }
}



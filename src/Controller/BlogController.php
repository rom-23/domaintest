<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Exception;
use Knp\Snappy\Image;
use mikehaertl\wkhtmlto\Image as Images;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController

{
    /**
     * @Route("/blog", name="blog")
     * @param ArticleRepository $repo
     * @return Response
     */
    public function index(ArticleRepository $repo): Response
    {
        $articles = $repo->findLatest();
        return $this->render('blog/index.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/blog/bundle/{id}", name="blog.showBundle")
     * @param Article $article
     * @param Image $knpSnappy
     * @return Response
     * @throws Exception
     * Utilise bundle symfony KnpSnappyBundle(wkhtmltoimage) pour creer un .jpg temporaire, puis envoi l'url a la vue
     */
    public function showBundle(Article $article, Image $knpSnappy)
    {
        $urlToScreen = $article->getContent();
        $knpSnappy->setOption('height', 1024);
        $knpSnappy->setOption('width', 968);
        $knpSnappy->setOption('quality', 18);
        $knpSnappy->setOption('disable-javascript', true);
        //$knpSnappy->setOption('disable-local-file-access', true);
        //$knpSnappy->setOption('disable-smart-width', false);
        //$knpSnappy->setOption('stop-slow-scripts', true);
        //$knpSnappy->setOption('crop-h', 200);
        //$knpSnappy->setOption('crop-w', 400);
        $filepath = 'images/tempImgFile.jpg';
        $knpSnappy->generate($article->getContent(), $filepath, [], true);
        //$size = getimagesize('images/tempImgFile.jpg');
        return $this->render('blog/show.html.twig', [
            'test' => $filepath,
            'description' => $article
        ]);
    }

    /**
     * @Route("/blog/quick", name="blog.showQuick")
     * @param Request $request
     * @param Image $knpSnappy
     * @return Response
     * @throws Exception
     */
    public function showQuick(Request $request, Image $knpSnappy)
    {
       // $request = $this->getRequest();
        $data = $request->request->get('search');
        $knpSnappy->setOption('height', 1024);
        $knpSnappy->setOption('width', 968);
        $knpSnappy->setOption('quality', 15);
        $knpSnappy->setOption('disable-javascript', false);
        //$knpSnappy->setOption('disable-local-file-access', true);
        //$knpSnappy->setOption('disable-smart-width', false);
        //$knpSnappy->setOption('stop-slow-scripts', true);
        //$knpSnappy->setOption('crop-h', 100);
        //$knpSnappy->setOption('crop-y', 100);
        $filepath = 'images/tempImgFile.jpg';
        $knpSnappy->generate($data, $filepath, [], true);
        //$size = getimagesize('images/tempImgFile.jpg');
        return $this->render('blog/quick.html.twig', [
            'test' => $filepath
        ]);
    }
    /**
     * @Route("/blog/openJpg/{id}", name="blog.openJpg")
     * @param Article $article
     * @return void
     * Affiche le jpg entier du site
     */
    public function openJpg(Article $article)
    {
        $image = new Images('http://'.$article->getContent());
        $image->send();
    }

    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('blog/home.html.twig');
    }

}

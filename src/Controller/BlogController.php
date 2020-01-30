<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Exception;
use Knp\Snappy\Image;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
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
     * @Route("/blog/iframe/{id}", name="blog.showIframe")
     * @param Article $article
     * @return Response
     * @throws Exception
     * Affiche le site dans une iframe
     */
    public function showIframe(Article $article)
    {
        return $this->render('blog/show.html.twig', [
            'urlToScreen' => $article->getContent(),
            'screenSh' => ""
        ]);
    }

     /**
     * @Route("/blog/bundle/{id}", name="blog.showBundle")
     * @param Article $article
     * @param Image $knpSnappy
     * @return Response
     * @throws Exception
     * Utilise le bundle symfony KnpSnappyBundle (wkhtmltoimage) pour creer un .jpg et l'ouvrir
     */
    public function showBundle(Article $article, Image $knpSnappy)
    {
        // Get snappy.image service
        $imageGenerator = $knpSnappy;
        $imageGenerator->setOption('height', 800);
        $imageGenerator->setOption('width', 600);
        $imageGenerator->setOption('quality', 100);
        $imageGenerator->setOption('disable-javascript', false);

        $filepath = 'images/' . $article->getId() . 'screenshot.jpg';
        $imageGenerator->generate($article->getContent(), $filepath);

        $response = new BinaryFileResponse($filepath);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE);
        return $response;
    }

    /**
     * @Route("/blog/api/{id}", name="blog.showGoogle")
     * @param Article $article
     * @return Response
     * @throws Exception
     * Utilise l'API Google PageSpeed Insights pour afficher le site en screenshot
     */
    public function showGoogle(Article $article)
    {
        $siteURL = $article->getContent();
        if (filter_var($siteURL, FILTER_VALIDATE_URL)) {
            $googlePagespeedData = file_get_contents("https://www.googleapis.com/pagespeedonline/v2/runPagespeed?url=$siteURL&screenshot=true");
            $googlePagespeedData = json_decode($googlePagespeedData, true);
            $screenshot = $googlePagespeedData['screenshot']['data'];
            $screenshot = str_replace(array('_', '-'), array('/', '+'), $screenshot);

            return $this->render('blog/show.html.twig', [
                'urlToScreen' => "",
                'screenSh' => $screenshot
            ]);
        }else{
            return null;
        }
    }

    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('blog/home.html.twig', [
            'title' => 'hello',
            'age' => 31
        ]);
    }

}

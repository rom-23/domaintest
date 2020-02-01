<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Exception;
use Knp\Snappy\Image;
//use mikehaertl\wkhtmlto\Image;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
//use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
//use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
//use Symfony\Component\BrowserKit\HttpBrowser;
//use Symfony\Component\HttpClient\HttpClient;
//use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
//use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
//use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
//use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
//use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

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

//    /**
//     * @Route("/blog/iframe/{id}", name="blog.showIframe")
//     * @param Article $article
//     * @return Response
//     * @throws Exception
//     * Affiche le site dans une iframe
//     */
//    public function showIframe(Article $article)
//    {
//        return $this->render('blog/show.html.twig', [
//            'urlToScreen' => $article->getContent(),
//            'screenSh' => ""
//        ]);
//    }

    /**
     * @Route("/blog/bundle/{id}", name="blog.showBundle")
     * @param Article $article
     * @param Image $knpSnappy
     * @return Response
     * @throws Exception Utilise le bundle symfony KnpSnappyBundle (wkhtmltoimage) pour creer un .jpg et l'ouvrir
     */
    public function showBundle(Article $article, Image $knpSnappy)
    {
        $urlToScreen=$article->getContent();
        // Get snappy.image service
        $imageGenerator = $knpSnappy;
        $imageGenerator->setOption('height', 1024);
        $imageGenerator->setOption('width', 800);
        $imageGenerator->setOption('quality', 50);
        $imageGenerator->setOption('disable-javascript', false);
        //$imageGenerator->setOption('disable-local-file-access', true);
    //$imageGenerator->setOption('disable-smart-width', false);
        //$imageGenerator->setOption('stop-slow-scripts', true);
//        $imageGenerator->setOption('crop-h', 100);
//        $imageGenerator->setOption('crop-y', 100);
        $filepath = 'images/tempImgFile.jpg';
        $imageGenerator->generate($article->getContent(), $filepath,[],true);
        //$size = getimagesize('images/tempImgFile.jpg');
        //dd($size);
        //$response = new BinaryFileResponse($filepath);
        //$response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE);
        return $this->render('blog/show.html.twig', [
                'test' => $filepath,
            'urlToScreen'=>$urlToScreen
            ]);
    }

//    /**
//     * @Route("/blog/api/{id}", name="blog.showGoogle")
//     * @param Article $article
//     * @return Response
//     * @throws Exception
//     * Utilise l'API Google PageSpeed Insights pour afficher le site en screenshot
//     */
//    public function showGoogle(Article $article)
//    {
//        $siteURL = $article->getContent();
//
//        if (filter_var($siteURL, FILTER_VALIDATE_URL)) {
//            $googlePagespeedData = file_get_contents("https://www.googleapis.com/pagespeedonline/v2/runPagespeed?url=$siteURL&screenshot=true");
//            $googlePagespeedData = json_decode($googlePagespeedData, true);
//            $screenshot = $googlePagespeedData['screenshot']['data'];
//            $screenshot = str_replace(array('_', '-'), array('/', '+'), $screenshot);
//
//            return $this->render('blog/show.html.twig', [
//                'urlToScreen' => "",
//                'screenSh' => $screenshot
//            ]);
//        } else {
//            return null;
//        }
//    }
//
//    /**
//     * @Route("/blog/browser/{id}", name="blog.showBrowser")
//     * @param Article $article
//     * @return Response
//     */
//    public function showBrowser(Article $article)
//    {
//        $browser = new HttpBrowser(HttpClient::create());
//        $browser->request('GET', $article->getContent());
////        return $this->render('blog/show.html.twig', [
////            'urlToScreen' => "",
////            'screenSh' => new Response($browser->getResponse())
////        ]);
//        return new Response($browser->getResponse());
//    }
//
//    /**
//     * @Route("/blog/bro/{id}", name="blog.showBro")
//     * @param Article $article
//     * @return Response
//     */
//    public function showBro(Article $article)
//    {
//        $image = new Image($article->getContent());
//        $data = $image->toString();
//
//        $im = imagecreatefromstring($data);
//        $size = getimagesize('images/test.jpg');
//
//        if ($im !== false) {
//            //header('Content-Type: image/png');
//            //$image->send();
//            imagepng($im, 'images/test.jpg', -1, -1);
//            imagedestroy($im);
//            return $this->render('blog/show.html.twig', [
//                'test' => 'images/test.jpg',
//                'imgInfo' => $size
//            ]);
//        }
//    }

//    /**
//     * @Route("/blog/http/{id}", name="blog.showhttp")
//     * @param Article $article
//     * @return string
//     * @throws ClientExceptionInterface
//     * @throws DecodingExceptionInterface
//     * @throws RedirectionExceptionInterface
//     * @throws ServerExceptionInterface
//     * @throws TransportExceptionInterface
//     */
//    public function showhttp(Article $article)
//    {
//        $client = HttpClient::create();
//        $response = $client->request('GET', $article->getContent());
//        $statusCode = $response->getStatusCode();
//        $contentType = $response->getHeaders()['content-type'][0];
//        $content = $response->getContent();
//        //$content = $response->toArray();
//        return $this->render('blog/show.html.twig', [
//            'test' => new Response($response->getContent())
//        ]);
//    }

    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('blog/home.html.twig');
    }

}

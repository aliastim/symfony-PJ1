<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\DBAL\Schema\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Request;


class DefaultController extends Controller
{
    /**
     * @Route("/h", name="homepage")
     */
    public function index()
    {
        /*$em = $this->getDoctrine()->getManager();
        $i = 0;

        while ($i < 10000)
        {
            $article = new Article();
            $article->setTitle('La jolie vie de Bob le chien');
            $article->setSlug('lajolieviedeboblechien');
            $article->setContent('Il était une fois, un petit chien dont le nom était Bob, on lappelait bob le chien parce que cétait un chien et quil avait une tête ronde donc on lappelalit Bob');
            $article->setExcerpt('Il était une fois, un petit chien dont');

            $em->persist($article);

            if (0 === $i % 500)
            {
                $em->flush();
            }

            $i++;

            $em->flush();
        }*/

        $em   = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Article::class);

        $articles = $repo->findAll();
        //dump($articles);


        /*return new JsonResponse(['name' => 'Tim']);*/
        /*return new Response('coucou');*/
        return $this->render('homepage.html.twig', [
            'articles' => $articles,
        ]);
    }

    /*
    /**
     * @Route("/h", name="homepage2")
     * @param Request $request
     * @return Response
     *//*
    public function listAction(Request $request)
    {
        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT a FROM AcmeMainBundle:Article a";
        //$em   = $this->getDoctrine()->getManager();
        //$dql = $em->getRepository(Article::class);

        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        // parameters to template
        return $this->render('homepage.html.twig', array('pagination' => $pagination));
    }*/

    /**
     * @Route("/", name="homepage")
     */
    public function page()
    {
        $page = $_GET['page'] ?? 1;

        /** @var \App\Repository\ArticleRepository $repo */
        /*$repo     = $entityManager->getRepository(User::class);*/
        $em   = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Article::class);

        $articles = $repo->loadAll(Article::MAX_PER_PAGE, ($page - 1) * 10);
        $count    = $repo->count([]);
        $maxPagination  = (int)ceil($count / Article::MAX_PER_PAGE);
        $minPage = (int) max(1, ($page-5));
        $maxPage = (int) max($page, ($page+5));
        $max = 0;

        while(abs($minPage - $maxPage) < 10){

            if ($minPage > 1)
            {
                $minPage--;
            }

            if ($maxPage < $maxPagination)
            {
                $maxPage++;
            }

            $max++;

            if ($max > 10)
            {
                break;
            }
        }

        return $this->render('homepage.html.twig', [
            'currentPage' => $page,
            'maxPagination' => $maxPagination,
            'minPage' => $minPage,
            'maxPage' => $maxPage,
            'articles' => $articles,
            'isConnected' => isset($_SESSION),
        ]);
    }

    /**
     * @Route("/article/{slug}", name="article")
     *
     * @param string $slug
     *
     * @return Response
     *
     */


    public function article(string $slug)
    {
        /*$this->render('article.html.twig',
            [
                'article' => $article,
            ]);*/

        $em      = $this->getDoctrine()->getManager();
        $repo    = $em->getRepository(Article::class);
        $article = $repo->findOneBy([
            'slug' => $slug,
        ]);
        // 404 ou page qui affiche l'article

        return $this->render('article.html.twig', [
            'article' => $article,
            'isConnected' => isset($_SESSION),
        ]);

        /*die($slug);*/
    }

}

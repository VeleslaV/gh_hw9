<?php

namespace Veles\HomeWorkBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Yaml;
use Veles\HomeWorkBundle\Entity\Gbook;
use Veles\HomeWorkBundle\Form\Type\GbookType;

class HomeWorkController extends Controller
{
    protected function createPageObject($pageLink)
    {
        $repository = $this->getDoctrine()->getRepository('VelesHomeWorkBundle:Page');
        $pageObj = $repository->findOneBy(array('name' => $pageLink));

        $pageData = array(
            'pageName' => $pageLink,
            'pageContent' => $pageObj
        );

        return $pageData;
    }

    public function mainAction()
    {
        $pageData = $this->createPageObject("main");

        $repository = $this->getDoctrine()->getRepository('VelesHomeWorkBundle:Article');
        $articleObj = $repository->findBy(array(), array('created' => 'ASC'));

        $pageData['posts'] = $articleObj;

        return $this->render('VelesHomeWorkBundle:HomeWork:main.html.twig', $pageData);
    }

    public function aboutAction()
    {
        $pageData = $this->createPageObject("about");

        return $this->render('VelesHomeWorkBundle:HomeWork:about.html.twig', $pageData);
    }


    //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Gbook >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    public function gbookAction(Request $request)
    {
        $pageData = $this->createPageObject("gbook");
        $commentsQuery = $this->getCommentsData();

        $paginator  = $this->get('knp_paginator');
        $p_options = Yaml::parse($this->getYmlFile());

        $pagination = $paginator->paginate(
            $commentsQuery,
            $this->get('request')->query->get('page', $p_options['start_from']),$p_options['posts_per_page']
        );

        $pageData['comments'] = $commentsQuery;
        $pageData['pagination'] = $pagination;

        $gbook = new Gbook();
        $form = $this->createForm(new GbookType(), $gbook);
        $form->handleRequest($request);
        $pageData['form'] = $form->createView();

        if ($form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($gbook);
            $manager->flush();

            return $this->redirect($this->generateUrl('_comment_success'));
        }

        return $this->render('VelesHomeWorkBundle:HomeWork:gbook.html.twig', $pageData);
    }

    public function oneCommentAction($cid = "")
    {
        $pageData = $this->createPageObject("comment");

        if($cid == ""){
            $pageData['oneComment']['error'] = "No comment id =(";
        }else{
            $pageData['oneComment'] = $this->getCommentsData($cid);
        }

        return $this->render('VelesHomeWorkBundle:HomeWork:comment.html.twig', $pageData);
    }

    public function getCommentsData($cid = "")
    {
        $repository = $this->getDoctrine()->getRepository('VelesHomeWorkBundle:Gbook');
        $em = $this->getDoctrine()->getManager();

        if($cid == ""){
            //$commentObj = $repository->findAllOrderedById();
            $commentObj = $em->createQuery('SELECT g FROM VelesHomeWorkBundle:Gbook g ORDER BY g.id DESC');
        }else{
            $commentObj = $repository->find($cid);
        }

        if (!$commentObj) {
            throw $this->createNotFoundException('No comments found =(');
        }

        return $commentObj;
    }

    //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Articles >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    public function latestArticleAction()
    {
        $repositoryArticles = $this->getDoctrine()->getRepository('VelesHomeWorkBundle:Article');
        $articlesObj = $repositoryArticles->findLatestArticlesLimit("6");
        $pageData['latestArticles'] = $articlesObj;

        $mostViewedArticlesObj = $repositoryArticles->findMostViewedArticlesLimit("6");
        $pageData['mostViewedArticles'] = $mostViewedArticlesObj;

        $repositoryComments = $this->getDoctrine()->getRepository('VelesHomeWorkBundle:Gbook');
        $commentsObj = $repositoryComments->findLatestCommentsLimit("9");
        $pageData['latestComments'] = $commentsObj;

        $repositoryTags = $this->getDoctrine()->getRepository('VelesHomeWorkBundle:Tag');
        $tagsObj = $repositoryTags->findAll();
        $pageData['allTags'] = $tagsObj;

        return $this->render('VelesHomeWorkBundle:HomeWork/Modules:sidebar.html.twig', $pageData);
    }

    public function oneArticleAction($aid = "")
    {
        $pageData = array(
            'pageName' => "article"
        );

        if(empty($aid)){
            $pageData['oneArticle']['error'] = "No article id =(";
        }else{
            $pageData['oneArticle'] = $this->getArticleData($aid);
        }

        return $this->render('VelesHomeWorkBundle:HomeWork:article.html.twig', $pageData);
    }

    public function getArticleData($aid)
    {
        $repository = $this->getDoctrine()->getRepository('VelesHomeWorkBundle:Article');

        if(empty($aid)){
            throw $this->createNotFoundException('No articles found =(');
        }else{
            $articleObj = $repository->find($aid);
        }

        return $articleObj;
    }

    //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Categories >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    public function oneCategoryAction($catid = "")
    {
        $pageData = $this->createPageObject("category");

        if(empty($catid)){
            $pageData['oneCategory']['error'] = "No category id =(";
        }else{
            $pageData['oneCategory'] = $this->getCategoryData($catid);
        }

        return $this->render('VelesHomeWorkBundle:HomeWork:category.html.twig', $pageData);
    }

    public function getCategoryData($catid)
    {
        $repository = $this->getDoctrine()->getRepository('VelesHomeWorkBundle:Category');

        if(empty($catid)){
            throw $this->createNotFoundException('No categories found =(');
        }else{
            $categoryObj = $repository->findBy(array('name' => $catid), array('id' => 'DESC'));
        }

        return $categoryObj;
    }

    //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Tags >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    public function oneTagAction($tid = "")
    {
        $pageData = $this->createPageObject("tag");

        if(empty($tid)){
            $pageData['oneTag']['error'] = "No tag id =(";
        }else{
            $pageData['oneTag'] = $this->getTagData($tid);
        }

        return $this->render('VelesHomeWorkBundle:HomeWork:tag.html.twig', $pageData);
    }

    public function getTagData($tid)
    {
        $repository = $this->getDoctrine()->getRepository('VelesHomeWorkBundle:Tag');

        if(empty($tid)){
            throw $this->createNotFoundException('No tags found =(');
        }else{
            $tagObj = $repository->findBy(array('name' => $tid), array('id' => 'DESC'));
        }

        return $tagObj;
    }


    //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Tech >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    public function successAddAction()
    {
        $pageData = $this->createPageObject("success");
        return $this->render('VelesHomeWorkBundle:HomeWork:success.html.twig', $pageData);
    }

    protected function getYmlFile()
    {
        return __DIR__ . '/../Resources/config/paginator.yml';
    }
}
<?php

namespace Veles\HomeWorkBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;
use Veles\HomeWorkBundle\Entity\Gbook;
use Veles\HomeWorkBundle\Entity\Article;
use Veles\HomeWorkBundle\Form\Type\GbookType;
use Veles\HomeWorkBundle\Form\Type\ArticleType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Collection;

class HomeWorkController extends Controller
{
    protected function createPageObject($pageLink)
    {
        $repository = $this->getDoctrine()->getRepository('VelesHomeWorkBundle:Page');
        $pageObj = $repository->findOneBy(array('name' => $pageLink));

        $pageData = array(
            'sectionData' => $pageObj
        );

        return $pageData;
    }

    public function mainAction()
    {
        $pageData = $this->createPageObject("main");

        $repository = $this->getDoctrine()->getRepository('VelesHomeWorkBundle:Article');
        $articlesObj = $repository->findLatestArticlesLimit("4");

        if(empty($articlesObj)){
            $pageData['resultData']['error'] = "No articles found =(";
        }else{
            $pageData['resultData'] = $articlesObj;
        }

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

        $pageData['pagination'] = $pagination;

        $gbook = new Gbook();
        $form = $this->createForm(new GbookType(), $gbook);
        $form->handleRequest($request);
        $pageData['form'] = $form->createView();

        if ($form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($gbook);
            $manager->flush();

            return $this->redirect($this->generateUrl('_gbook'));
        }

        return $this->render('VelesHomeWorkBundle:HomeWork:gbook.html.twig', $pageData);
    }

    public function oneCommentAction($cid = "")
    {
        $pageData = $this->createPageObject("comment");

        if($cid == ""){
            $pageData['resultData']['error'] = "No comment id =(";
        }else{
            $pageData['resultData'] = $this->getCommentsData($cid);
        }

        return $this->render('VelesHomeWorkBundle:HomeWork:comment.html.twig', $pageData);
    }

    public function getCommentsData($cid = "")
    {
        if($cid == ""){
            $repository = $this->getDoctrine()->getRepository('VelesHomeWorkBundle:Gbook');
            $query = $repository->createQueryBuilder('g')
                ->orderBy('g.id', 'DESC')
                ->getQuery();

            $commentObj = $query;
        }else{
            $repository = $this->getDoctrine()->getRepository('VelesHomeWorkBundle:Gbook');
            $commentObj = $repository->find($cid);
        }

        if (!$commentObj) {
            throw $this->createNotFoundException('No comments found =(');
        }

        return $commentObj;
    }

    public function getCommentsCountAction($aid)
    {
        $thread = $this->container
            ->get('fos_comment.manager.thread')
            ->findThreadById($aid);
        $numOfComments = is_object($thread) ? $thread->getNumComments() : 0;

        //var_dump($numOfComments); die;

        return new Response($numOfComments);
    }

    //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Articles >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    public function latestArticleAction()
    {
        $pageData = array();

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
        $pageData = array();

        if(empty($aid)){
            $pageData['resultData']['error'] = "No article id =(";
        }else{
            $pageData['resultData']['article'] = $this->getArticleData($aid);
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

    public function addArticleAction(Request $request)
    {
        $pageData = array();

        $pageData['sectionData']['title'] = "Add new";

        $article = new Article();
        $form = $this->createForm(new ArticleType(), $article);
        $form->handleRequest($request);

        $pageData['form'] = $form->createView();

        if($form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $tags = $article->getTags();

            $article
                ->setTags($tags)
                ->setCreated(new \DateTime());

            $manager->persist($article);
            $manager->flush();

            return $this->redirect($this->generateUrl('_main'));
        }

        return $this->render('VelesHomeWorkBundle:HomeWork:article_add.html.twig', $pageData);
    }

    //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Categories >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    public function oneCategoryAction($catid = "")
    {
        $pageData = $this->createPageObject("category");

        if(empty($catid)){
            $pageData['resultData']['error'] = "No category id =(";
        }else{
            $pageData['resultData'] = $this->getCategoryData($catid);
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
            $pageData['resultData']['error'] = "No tag id =(";
        }else{
            $pageData['resultData'] = $this->getTagData($tid);
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

    //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Search >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    public function searchFormAction(Request $request)
    {
        $pageData = array();

        $form = $this->createFormBuilder()
            ->add('keyword', 'text', array(
                'constraints' => array(
                    new NotBlank(),
                    new Length(array('min' => 3)),
                ),
            ))
            ->getForm();

        $form->handleRequest($request);
        $pageData['form'] = $form->createView();

        if ($form->isValid()) {
            $data = $form->getData();
            $keyword = $data['keyword'];

            return $this->redirect($this->generateUrl('_search_keyword', array('keyword' => $keyword), true));
        }

        return $this->render('VelesHomeWorkBundle:HomeWork/Modules:search_form.html.twig', $pageData);
    }

    public function searchResultAction($keyword = "")
    {
        $pageData = array();

        if(empty($keyword)){
            $pageData['resultData']['error'] = "No keyword for search =(";
        }else{
            $repository = $this->getDoctrine()->getRepository('VelesHomeWorkBundle:Article');
            $searchQuery = $repository->createQueryBuilder('a')
                ->where('a.body LIKE :keyword OR a.title LIKE :keyword')
                ->setParameter('keyword', '%'.$keyword.'%')
                ->getQuery();

            $paginator  = $this->get('knp_paginator');
            $p_options = Yaml::parse($this->getYmlFile());

            $pagination = $paginator->paginate(
                $searchQuery,
                $this->get('request')->query->get('page', $p_options['start_from']),$p_options['search_result_per_page']
            );

            $pageData['kwd'] = $keyword;
            $pageData['pagination'] = $pagination;
        }

        return $this->render('VelesHomeWorkBundle:HomeWork:search.html.twig', $pageData);
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
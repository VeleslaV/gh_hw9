<?php

namespace Veles\HomeWorkBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Veles\HomeWorkBundle\Controller\HomeWork;

class AjaxController extends Controller
{
    public function ajaxUpdateArticleViewsAction(Request $request){
        $articleId = $request->get('id');

        $em = $this->getDoctrine()->getManager();
        $oneArticle = $em->getRepository('VelesHomeWorkBundle:Article')->find($articleId);

        $currentViews = $oneArticle->getViews();
        $currentViews++;

        $oneArticle->setViews($currentViews);
        $em->flush();

        $response = array("code" => 100, "success" => true, "id" => $articleId);

        return new Response(json_encode($response));
    }

    public function ajaxLoadMoreArticleAction($page)
    {
        $limit = 4;
        $offset = ($limit * ($page - 1)) + 1;

        $repository = $this->getDoctrine()->getRepository('VelesHomeWorkBundle:Article');
        $articlesObj = $repository->findArticlesOffsetLimit($offset, $limit);
        $pageData['posts'] = $articlesObj;

        $content = $this->renderView('VelesHomeWorkBundle:HomeWork:Modules/articles_template.html.twig', $pageData);

        return new Response($content);
    }
}
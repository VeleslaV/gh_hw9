<?php

namespace Veles\HomeWorkBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
}
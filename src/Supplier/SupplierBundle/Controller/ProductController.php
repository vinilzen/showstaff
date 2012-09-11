<?php

namespace Supplier\SupplierBundle\Controller;

use Supplier\SupplierBundle\Entity\Supplier;
use Supplier\SupplierBundle\Entity\Product;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Supplier\SupplierBundle\Form\Type\ProductType;

class ProductController extends Controller
{
	public $unit = array(	'1' => 'кг',
							'2' => 'литр',
							'3' => 'шт',
							'4' => 'пучок',
							'5' => 'бутылка',);
	
    /**
     * @Route("company/{cid}/product/{pid}/delete", name="product_del")
     */
    public function delAction($cid, $pid, Request $request)
    {
		$product = $this->getDoctrine()
						->getRepository('SupplierBundle:Product')
						->find($pid);
						
		if (!$product) {
			if ($request->isXmlHttpRequest()) 
			{
				$code = 200;
				$result = array('code' => $code, 'message' => 'No product found for id '.$pid);
				$response = new Response(json_encode($result), $code, array('Content-Type' => 'application/json'));
				$response->sendContent();
				die();
			}
			else
			{
				throw $this->createNotFoundException('No product found for id '.$pid);
			}
		}
		
		$em = $this->getDoctrine()->getEntityManager();				
		$em->remove($product);
		$em->flush();
		
		if ($request->isXmlHttpRequest()) 
		{
			$code = 200;
			$result = array('code' => $code, 'message' => 'Product #'.$pid.' is deleted');
			$response = new Response(json_encode($result), $code, array('Content-Type' => 'application/json'));
			$response->sendContent();
			die();
		}
		else
		{
			return $this->redirect($this->generateUrl('product'));
		}
    }
    

    /**
     * @Route("company/{cid}/product/create", name="product_create")
     * @Template()
     */    
    public function createAction($cid, Request $request)
    {
		$product = new Product();
		
		$form = $this->createForm(new ProductType($this->unit), $product);
					
		if ($request->getMethod() == 'POST')
		{			
			$validator = $this->get('validator');
			$form->bindRequest($request);

			if ($form->isValid())
			{
				$product = $form->getData();				
				$em = $this->getDoctrine()->getEntityManager();
				$em->persist($product);
				$em->flush();
				
				if ($request->isXmlHttpRequest()) 
				{
					$code = 200;
					$result = array('code' => $code, 'message' => 'Product #'.$product->getId().' is created');
					$response = new Response(json_encode($result), $code, array('Content-Type' => 'application/json'));
					$response->sendContent();
					die();
				}
				else
				{
					return $this->redirect($this->generateUrl('product'));
				}
			}
		}


		return array('form' => $form->createView());
	}
	
	
    /**
     * @Route("company/{cid}/product/{pid}/edit", name="product_edit")
     * @Template()
     */    
	public function editAction($cid, $pid, Request $request)
	{
		$product = $this->getDoctrine()
						->getRepository('SupplierBundle:Product')
						->findOneByIdJoinedToCompany($pid, $cid);
		
		if (!$product) {
			if ($request->isXmlHttpRequest()) 
			{
				$code = 400;
				$result = array('code' => $code, 'message' => 'No product found for id '.$pid);
				$response = new Response(json_encode($result), $code, array('Content-Type' => 'application/json'));
				$response->sendContent();
				die();
			}
			else
			{
				throw $this->createNotFoundException('No product found for id '.$pid);
			}
		}
		
		$company = $product->getCompany();
		
		$form = $this->createForm(new ProductType($this->unit), $product);
					
		if ($request->getMethod() == 'POST')
		{
			$validator = $this->get('validator');
			$form->bindRequest($request);

			if ($form->isValid())
			{
				$product = $form->getData();
				$product->setCompany($company);		
				$em = $this->getDoctrine()->getEntityManager();
				$em->persist($product);
				$em->flush();
				
				if ($request->isXmlHttpRequest()) 
				{
					$code = 200;
					$result = array('code' => $code, 'message' => 'Product #'.$pid.' is updated');
					$response = new Response(json_encode($result), $code, array('Content-Type' => 'application/json'));
					$response->sendContent();
					die();
				}
				else
				{
					return $this->redirect($this->generateUrl('product', array('cid' => $cid)));
				}
			}
		}


		return array('form' => $form->createView(), 'product' => $product, 'company' => $company);
	}
	
	
	/**
	 * @Route("/company/{cid}/product/{pid}", name="product_show", requirements={"_method" = "GET"})
	 * @Template()
	 */
	public function showAction($cid, $pid, Request $request)
	{		
		$product = $this->getDoctrine()
						->getRepository('SupplierBundle:Product')
						->findOneByIdJoinedToCompany($pid, $cid);
		
		if (!$product) {
			if ($request->isXmlHttpRequest()) 
			{
				$code = 200;
				$result = array('code' => $code, 'message' => 'No product found for id '.$pid);
				$response = new Response(json_encode($result), $code, array('Content-Type' => 'application/json'));
				$response->sendContent();
				die();
			}
			else
			{
				throw $this->createNotFoundException('No product found for id '.$pid);
			}
		}
		
		$company = $product->getCompany();
		
		return array('product' => $product, 'company' => $company, 'unit' => $this->unit);
	}
	
	
	/**
	 * @Route(	"company/{cid}/product", 
	 * 			name="product", 
	 * 			requirements={"_method" = "GET"})
	 * @Template()
	 */
	public function listAction($cid, Request $request)
	{
		$company = $this->getDoctrine()
						->getRepository('SupplierBundle:Company')
						->findAllProductsByCompany($cid);
		
		if (!$company) {
			if ($request->isXmlHttpRequest()) 
			{
				$code = 404;
				$result = array('code' => $code, 'message' => 'No company found for id '.$cid);
				$response = new Response(json_encode($result), $code, array('Content-Type' => 'application/json'));
				$response->sendContent();
				die();
			}
			else
			{
				throw $this->createNotFoundException('No company found for id '.$cid);
			}
		}

		$products = $company->getProducts();
			
		$products_array = array();
		
		if ($products)
		{
			foreach ($products AS $p)
				$products_array[] = array( 	'id' => $p->getId(),
											'name'=> $p->getName(), 
											'unit' => $p->getUnit(),
											);
		}
			
		if ($request->isXmlHttpRequest()) 
		{
			$code = 200;
			
			$result = array('code' => $code, 'data' => $products_array);
			$response = new Response(json_encode($result), $code, array('Content-Type' => 'application/json'));
			$response->sendContent();
			die(); 
		}
		
		return array('company' => $company, 'products_json' => json_encode($products_array));
	}
	
	/**
	 * @Route(	"company/{cid}/product/{pid}", 
	 * 			name="product_ajax_update", 
	 * 			requirements={"_method" = "PUT"})
	 */
	 public function ajaxupdateAction($cid, $pid, Request $request)
	 {		 
		$model = (array)json_decode($request->getContent());
		
		if (count($model) > 0 && isset($model['id']) && is_numeric($model['id']) && $pid == $model['id'])
		{
			$product = $this->getDoctrine()
							->getRepository('SupplierBundle:Product')
							->find($model['id']);
			
			if (!$product)
			{
				$code = 404;
				$result = array('code' => $code, 'message' => 'No product found for id '.$pid);
				$response = new Response(json_encode($result), $code, array('Content-Type' => 'application/json'));
				$response->sendContent();
				die();
			}
			
			$validator = $this->get('validator');

			$product->setName($model['name']);
			$product->setUnit((int)$model['unit']);
			
			$errors = $validator->validate($product);
			
			if (count($errors) > 0) {
				
				foreach($errors AS $error)
					$errorMessage[] = $error->getMessage();
				
				$code = 400;
				$result = array('code'=>$code, 'message'=>$errorMessage);
				$response = new Response(json_encode($result), $code, array('Content-Type' => 'application/json'));
				$response->sendContent();
				die();
				
			} else {
				
				$em = $this->getDoctrine()->getEntityManager();
				$em->persist($product);
				$em->flush();
				
				$code = 200;
				
				$result = array('code'=> $code, 'data' => array('name' => $product->getName(), 'unit' => $product->getUnit()));
				$response = new Response(json_encode($result), $code, array('Content-Type' => 'application/json'));
				$response->sendContent();
				die();
			
			}
		}
			
		$code = 400;
		$result = array('code'=> $code, 'message' => 'Invalid request');
		$response = new Response(json_encode($result), $code, array('Content-Type' => 'application/json'));
		$response->sendContent();
		die();
		 
	 }
	 
	
	/**
	 * @Route(	"company/{cid}/product/{pid}", 
	 * 			name="product_ajax_delete", 
	 * 			requirements={"_method" = "DELETE"})
	 */
	public function ajaxdeleteAction($cid, $pid, Request $request)
	{
		$product = $this->getDoctrine()
					->getRepository('SupplierBundle:Product')
					->find($pid);
					
		if (!$product)
		{
			$code = 404;
			$result = array('code' => $code, 'message' => 'No product found for id '.$pid);
			$response = new Response(json_encode($result), $code, array('Content-Type' => 'application/json'));
			$response->sendContent();
			die();
		}
		

		$em = $this->getDoctrine()->getEntityManager();				
		$em->remove($product);
		$em->flush();
		
		$code = 200;
		$result = array('code' => $code, 'data' => $pid);
		$response = new Response(json_encode($result), $code, array('Content-Type' => 'application/json'));
		$response->sendContent();
		die();
	}
	

	/**
	 * @Route(	"company/{cid}/product", 
	 * 			name="product_ajax_create", 
	 * 			requirements={"_method" = "POST"})
	 */
	public function ajaxcreateAction($cid, Request $request)
	{
		$company = $this->getDoctrine()
						->getRepository('SupplierBundle:Company')
						->find($cid);
						
		if (!$company) {
			$code = 404;
			$result = array('code' => $code, 'message' => 'No company found for id '.$cid);
			$response = new Response(json_encode($result), $code, array('Content-Type' => 'application/json'));
			$response->sendContent();
			die();
		}
		
		$model = (array)json_decode($request->getContent());
		
		//print_r($model); die();
		
		if (count($model) > 0 && isset($model['unit']) && isset($model['name']))
		{
			$validator = $this->get('validator');
			$product = new Product();
			$product->setName($model['name']);
			$product->setUnit((int)$model['unit']);
			
			$errors = $validator->validate($product);
			
			if (count($errors) > 0) {
				
				foreach($errors AS $error)
					$errorMessage[] = $error->getMessage();
					
				$code = 400;
				$result = array('code' => $code, 'message'=>$errorMessage);
				$response = new Response(json_encode($result), $code, array('Content-Type' => 'application/json'));
				$response->sendContent();
				die();
				
			} else {
				
				$product->setCompany($company);
				$em = $this->getDoctrine()->getEntityManager();
				$em->persist($product);
				$em->flush();
				
				$code = 200;
				$result = array(	'code' => $code, 'data' => array(	'id' => $product->getId(),
																		'name' => $product->getName(), 
																		'unit' => $product->getUnit()
																	));
				
				$response = new Response(json_encode($result), $code, array('Content-Type' => 'application/json'));
				$response->sendContent();
				die();
			
			}
		}
		
		$code = 400;
		$result = array('code' => $code, 'message'=> 'Invalid request');
		$response = new Response(json_encode($result), $code, array('Content-Type' => 'application/json'));
		$response->sendContent();
		die();
	 
	}
}

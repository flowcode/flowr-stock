<?php

namespace Flower\StockBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Flower\StockBundle\Entity\Setting;
use Flower\StockBundle\Form\Type\SettingType;
use Doctrine\ORM\QueryBuilder;

/**
 * Setting controller.
 *
 * @Route("/admin/stock/setting")
 */
class SettingController extends Controller
{
    /**
     * Lists all Setting entities.
     *
     * @Route("/", name="admin_setting")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('FlowerStockBundle:Setting')->createQueryBuilder('s');
        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);
        return array(
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a Setting entity.
     *
     * @Route("/{id}/show", name="admin_setting_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(Setting $setting)
    {
        $editForm = $this->createForm(new SettingType(), $setting, array(
            'action' => $this->generateUrl('admin_setting_update', array('id' => $setting->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($setting->getId(), 'admin_setting_delete');

        return array(

            'setting' => $setting,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),

        );
    }

    /**
     * Displays a form to create a new Setting entity.
     *
     * @Route("/new", name="admin_setting_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $setting = new Setting();
        $form = $this->createForm(new SettingType(), $setting);

        return array(
            'setting' => $setting,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Setting entity.
     *
     * @Route("/create", name="admin_setting_create")
     * @Method("POST")
     * @Template("FlowerStockBundle:Setting:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $setting = new Setting();
        $form = $this->createForm(new SettingType(), $setting);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($setting);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_setting_show', array('id' => $setting->getId())));
        }

        return array(
            'setting' => $setting,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Setting entity.
     *
     * @Route("/{id}/edit", name="admin_setting_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(Setting $setting)
    {
        $editForm = $this->createForm(new SettingType(), $setting, array(
            'action' => $this->generateUrl('admin_setting_update', array('id' => $setting->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($setting->getId(), 'admin_setting_delete');

        return array(
            'setting' => $setting,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Setting entity.
     *
     * @Route("/{id}/update", name="admin_setting_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerStockBundle:Setting:edit.html.twig")
     */
    public function updateAction(Setting $setting, Request $request)
    {
        $editForm = $this->createForm(new SettingType(), $setting, array(
            'action' => $this->generateUrl('admin_setting_update', array('id' => $setting->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('admin_setting_show', array('id' => $setting->getId())));
        }
        $deleteForm = $this->createDeleteForm($setting->getId(), 'admin_setting_delete');

        return array(
            'setting' => $setting,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Setting entity.
     *
     * @Route("/{id}/delete", name="admin_setting_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(Setting $setting, Request $request)
    {
        $form = $this->createDeleteForm($setting->getId(), 'admin_setting_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($setting);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_setting'));
    }

    /**
     * Create Delete form
     *
     * @param integer $id
     * @param string $route
     * @return \Symfony\Component\Form\Form
     */
    protected function createDeleteForm($id, $route)
    {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'delete')))
            ->setAction($this->generateUrl($route, array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm();
    }

}

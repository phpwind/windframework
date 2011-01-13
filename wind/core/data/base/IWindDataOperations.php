<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */

interface IWindDataOperations{
	public  function delete(IWindEntity $entity);
	public  function deleteBy(IWindEntity $entity);
	public  function find(IWindEntity $entity);
	public  function findBy(IWindEntity $entity);
	public  function update(IWindEntity $entity);
	public  function updateBy(IWindEntity $entity);
	public  function save(IWindEntity $entity);
}
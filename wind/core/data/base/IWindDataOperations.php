<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */

interface IWindDataOperations{
	public  function delete($obj);
	public  function deleteBy();
	public  function find();
	public  function findBy();
	public  function update();
	public  function updateBy();
	public  function save();
}
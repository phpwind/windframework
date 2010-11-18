<?php /* Smarty version Smarty3-RC3, created on 2010-11-18 04:27:56
         compiled from "D:\PHPAPP\phpwindframework\trunk\demos\smartyapp/templates\body.phtml" */ ?>
<?php /*%%SmartyHeaderCode:101554ce4ab4c8eb3e7-57350710%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '27f5797aec65d75910f094ce9696b51671b57c62' => 
    array (
      0 => 'D:\\PHPAPP\\phpwindframework\\trunk\\demos\\smartyapp/templates\\body.phtml',
      1 => 1290054010,
    ),
  ),
  'nocache_hash' => '101554ce4ab4c8eb3e7-57350710',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php $_template = new Smarty_Internal_Template("head.phtml", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>
<body>
<?php echo $_smarty_tpl->getVariable('name')->value;?>
 say: <?php echo $_smarty_tpl->getVariable('content')->value;?>

</body>
<?php $_template = new Smarty_Internal_Template("foot.phtml", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>
<?php /* Smarty version 2.6.18, created on 2015-09-22 12:08:51
         compiled from network-index-list.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 't', 'network-index-list.html', 18, false),array('function', 'rv_add_session_token', 'network-index-list.html', 37, false),array('function', 'ox_column_class', 'network-index-list.html', 90, false),array('function', 'ox_column_title', 'network-index-list.html', 91, false),array('function', 'cycle', 'network-index-list.html', 132, false),array('function', 'ox_entity_id', 'network-index-list.html', 146, false),array('modifier', 'count', 'network-index-list.html', 99, false),array('modifier', 'escape', 'network-index-list.html', 143, false),)), $this); ?>


<div class='tableWrapper'>
    <div class='tableHeader'>
        <ul class='tableActions'>
            <li>
                <a href='network-edit.php' class='inlineIcon iconAdvertiserAdd'><?php echo OA_Admin_Template::_function_t(array('str' => 'AddNetwork'), $this);?>
</a>
            </li>
            <li class='inactive activeIfSelected'>
                <a id='deleteSelection' href='#' class='inlineIcon iconDelete'><?php echo OA_Admin_Template::_function_t(array('str' => 'Delete'), $this);?>
</a>

                <?php echo '
                <script type=\'text/javascript\'>
                    <!--

                    $(\'#deleteSelection\').click(function(event) {
                        event.preventDefault();

                        if (!$(this).parents(\'li\').hasClass(\'inactive\')) {
                            var ids = [];
                            $(this).parents(\'.tableWrapper\').find(\'.toggleSelection input:checked\').each(function() {
                                ids.push(this.value);
                            });

                            if (!tablePreferences.warningBeforeDelete || confirm("'; ?>
<?php echo OA_Admin_Template::_function_t(array('str' => 'ConfirmDeleteNetworks'), $this);?>
<?php echo '")) {
                                window.location = \'network-delete.php?'; ?>
<?php echo OA_Admin_Template::_add_session_token(array(), $this);?>
<?php echo '&networkid=\' + ids.join(\',\');
                            }
                        }
                    });

                    //-->
                </script>
                '; ?>

            </li>
        </ul>

        <ul class='tableFilters alignRight'>
            <li>
                <div class='label'>
                    Show
                </div>

                <div class='dropDown'>
                    <span><span><?php if ($this->_tpl_vars['hideinactive']): ?>Active networks<?php else: ?>All networks<?php endif; ?></span></span>

                    <div class='panel'>
                        <div>
                            <ul>
                                <li><a href='network-index.php?hideinactive=0'>All networks</a></li>
                                <li><a href='network-index.php?hideinactive=1'>Active networks</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class='mask'></div>
                </div>
            </li>
            <?php if (! empty ( $this->_tpl_vars['topPager']->links )): ?>
            <li>
                <div class="pager">
                    <span class="controls"><?php echo $this->_tpl_vars['topPager']->links; ?>
</span>
                </div>
            </li>
            <?php endif; ?>
        </ul>

        <div class='clear'></div>

        <div class='corner left'></div>
        <div class='corner right'></div>
    </div>

    <table cellspacing='0' summary=''>
        <thead>
        <tr>
            <th class='first toggleAll'>
                <input type='checkbox' />
            </th>
            <th class='<?php echo OA_Admin_Template::_function_ox_column_class(array('item' => 'name','order' => 'up','default' => 1), $this);?>
'>
                <?php echo OA_Admin_Template::_function_ox_column_title(array('item' => 'name','order' => 'up','default' => 1,'str' => 'Name','url' => "network-index.php"), $this);?>

            </th>
            <th class='last alignRight'>&nbsp;

            </th>
        </tr>
        </thead>

        <?php if (! count($this->_tpl_vars['from'])): ?>
        <tbody>
        <tr class='odd'>
            <td colspan='3'>&nbsp;</td>
        </tr>
        <tr class='even'>
            <td colspan='3' class="hasPanel">
                <div class='tableMessage'>
                    <div class='panel'>

                        <?php if ($this->_tpl_vars['hideinactive']): ?>
                        <?php echo $this->_tpl_vars['aCount']['networks_hidden']; ?>
 <?php echo OA_Admin_Template::_function_t(array('str' => 'InactiveNetworksHidden'), $this);?>

                        <?php else: ?>
                        <?php echo OA_Admin_Template::_function_t(array('str' => 'NoNetworks'), $this);?>

                        <?php endif; ?>

                        <div class='corner top-left'></div>
                        <div class='corner top-right'></div>
                        <div class='corner bottom-left'></div>
                        <div class='corner bottom-right'></div>
                    </div>
                </div>

                &nbsp;
            </td>
        </tr>
        <tr class='odd'>
            <td colspan='3'>&nbsp;</td>
        </tr>
        </tbody>

        <?php else: ?>
        <tbody>
        <?php echo smarty_function_cycle(array('name' => 'bgcolor','values' => "even,odd",'assign' => 'bgColor','reset' => 1), $this);?>

        <?php $_from = $this->_tpl_vars['from']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['networkId'] => $this->_tpl_vars['client']):
?>
        <?php echo smarty_function_cycle(array('name' => 'bgcolor','assign' => 'bgColor'), $this);?>

        <tr class='<?php echo $this->_tpl_vars['bgColor']; ?>
 <?php if ($this->_tpl_vars['network']['type'] == $this->_tpl_vars['MARKET_TYPE']): ?>systemNetwork<?php endif; ?>'>
            <td class='toggleSelection'>
                <?php if ($this->_tpl_vars['network']['type'] != $this->_tpl_vars['MARKET_TYPE']): ?>
                <input type='checkbox' value='<?php echo $this->_tpl_vars['networkId']; ?>
' />
                <?php endif; ?>
            </td>
            <td>
                <?php if ($this->_tpl_vars['network']['type'] == $this->_tpl_vars['MARKET_TYPE']): ?>                 <span class='inlineIcon iconAdvertiserSystem'><?php echo ((is_array($_tmp=$this->_tpl_vars['network']['name'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</span>
                <?php else: ?>
                <a href='network-edit.php?networkid=<?php echo $this->_tpl_vars['networkId']; ?>
' class='inlineIcon iconAdvertiser'><?php echo ((is_array($_tmp=$this->_tpl_vars['network']['name'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</a>
                <?php echo OA_Admin_Template::_function_ox_entity_id(array('type' => 'Network','id' => $this->_tpl_vars['networkId']), $this);?>

                <?php endif; ?>
            </td>
            <td class='alignRight horizontalActions'>
                <ul class='rowActions'>
                    <li>
                        <a href='affiliate-edit.php?networkid=<?php echo $this->_tpl_vars['networkId']; ?>
' class='inlineIcon <?php if ($this->_tpl_vars['network']['type'] == $this->_tpl_vars['MARKET_TYPE']): ?>iconCampaignSystemAdd<?php else: ?>iconCampaignAdd<?php endif; ?>'><?php echo OA_Admin_Template::_function_t(array('str' => 'AddAffiliate'), $this);?>
</a>
                    </li>
                    <li>
                        <a href='network-affiliates.php?networkid=<?php echo $this->_tpl_vars['networkId']; ?>
' class='inlineIcon <?php if ($this->_tpl_vars['network']['type'] == $this->_tpl_vars['MARKET_TYPE']): ?>iconCampaignsSystem<?php else: ?>iconCampaigns<?php endif; ?>'><?php echo OA_Admin_Template::_function_t(array('str' => 'Affiliates'), $this);?>
</a>
                    </li>
                </ul>
            </td>
        </tr>
        <?php endforeach; endif; unset($_from); ?>
        </tbody>
        <?php endif; ?>
        <?php if (! empty ( $this->_tpl_vars['pager']->links )): ?>
        <tbody class="tableFooter">
        <tr>
            <td  colspan="3">
                <div class="pager">
                    <span class="summary"><?php echo $this->_tpl_vars['pager']->summary; ?>
</span>
                    <span class="controls"><?php echo $this->_tpl_vars['pager']->links; ?>
</span>
                </div>
            </td>
        </tr>
        </tbody>
        <?php endif; ?>
    </table>
</div>
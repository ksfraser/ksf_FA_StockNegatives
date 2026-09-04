<?php
declare(strict_types=1);

define('SS_ksf_FA_StockNegatives', 146 << 8);

class hooks_ksf_FA_StockNegatives extends hooks
{
    var $module_name = 'ksf_FA_StockNegatives';
    var $version = '2.4.19-1.0.0';

    function install_extension($company, $force = false)
    {
        parent::install_extension($company, $force);
        return true;
    }

    function activate_extension($company, $force = false)
    {
        add_security_section(SS_ksf_FA_StockNegatives, 'Stock Negatives Report', 'SA_INVENTORY');
        return true;
    }

    function deactivate_extension($company, $force = false)
    {
        remove_security_section(SS_ksf_FA_StockNegatives);
        return parent::deactivate_extension($company, $force);
    }

    function getModuleConstants(&$data, $opts = [])
    {
        $data['constants']['SS_ksf_FA_StockNegatives'] = SS_ksf_FA_StockNegatives;
        $data['constants']['SA_ksf_FA_STOCKNEGATIVES'] = SS_ksf_FA_StockNegatives | 1;
        return $data;
    }

    function getModuleCapabilities(&$data, $opts = [])
    {
        $data['capabilities']['stock_negatives_report'] = [
            'view' => 'SA_ksf_FA_STOCKNEGATIVES',
        ];
        return $data;
    }

    function prepend_page($page, $id)
    {
        global $path_to_root;

        if ($page === 'inventory' && $id === 'stock_negatives') {
            include_once $path_to_root . '/modules/ksf_FA_StockNegatives/pages/stock_negatives.php';
            exit;
        }
    }

    function hook_invoke_all($hook, &$data)
    {
        $autoload = __DIR__ . '/vendor/autoload.php';
        if (!file_exists($autoload)) {
            return null;
        }
        require_once $autoload;

        return parent::hook_invoke_all($hook, $data);
    }
}
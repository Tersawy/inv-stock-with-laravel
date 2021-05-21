<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permissions extends Model
{

    use SoftDeletes;

    const USER_VIEW     = "user_view";
    const USER_CREATE   = "user_create";
    const USER_UPDATE   = "user_update";
    const USER_TRASH    = "user_trash";
    const USER_RESTORE  = "user_restore";
    const USER_DELETE   = "user_delete";

    const USER_PERMISSION_VIEW     = "user_view";
    const USER_PERMISSION_CREATE   = "user_create";
    const USER_PERMISSION_UPDATE   = "user_update";
    const USER_PERMISSION_DELETE   = "user_delete";

    const PRODUCT_VIEW      = "product_view";
    const PRODUCT_CREATE    = "product_create";
    const PRODUCT_UPDATE    = "product_update";
    const PRODUCT_TRASH     = "product_trash";
    const PRODUCT_RESTORE   = "product_restore";
    const PRODUCT_DELETE    = "product_delete";
    const PRODUCT_BARCODE   = "product_barcode";
    const PRODUCT_IMPORT    = "product_import";

    const ADJUSTMENT_VIEW      = "adjustment_view";
    const ADJUSTMENT_CREATE    = "adjustment_create";
    const ADJUSTMENT_UPDATE    = "adjustment_update";
    const ADJUSTMENT_TRASH     = "adjustment_trash";
    const ADJUSTMENT_RESTORE   = "adjustment_restore";
    const ADJUSTMENT_DELETE    = "adjustment_delete";

    const TRANSFER_VIEW      = "transfer_view";
    const TRANSFER_CREATE    = "transfer_create";
    const TRANSFER_UPDATE    = "transfer_update";
    const TRANSFER_TRASH     = "transfer_trash";
    const TRANSFER_RESTORE   = "transfer_restore";
    const TRANSFER_DELETE    = "transfer_delete";

    const EXPENSE_VIEW      = "expense_view";
    const EXPENSE_CREATE    = "expense_create";
    const EXPENSE_UPDATE    = "expense_update";
    const EXPENSE_TRASH     = "expense_trash";
    const EXPENSE_RESTORE   = "expense_restore";
    const EXPENSE_DELETE    = "expense_delete";

    const SALES_VIEW      = "sales_view";
    const SALES_CREATE    = "sales_create";
    const SALES_UPDATE    = "sales_update";
    const SALES_TRASH     = "sales_trash";
    const SALES_RESTORE   = "sales_restore";
    const SALES_DELETE    = "sales_delete";

    const PURCHASE_VIEW      = "purchase_view";
    const PURCHASE_CREATE    = "purchase_create";
    const PURCHASE_UPDATE    = "purchase_update";
    const PURCHASE_TRASH     = "purchase_trash";
    const PURCHASE_RESTORE   = "purchase_restore";
    const PURCHASE_DELETE    = "purchase_delete";

    const QUOTATION_VIEW      = "quotation_view";
    const QUOTATION_CREATE    = "quotation_create";
    const QUOTATION_UPDATE    = "quotation_update";
    const QUOTATION_TRASH     = "quotation_trash";
    const QUOTATION_RESTORE   = "quotation_restore";
    const QUOTATION_DELETE    = "quotation_delete";

    const SALES_RETURN_VIEW      = "sales_return_view";
    const SALES_RETURN_CREATE    = "sales_return_create";
    const SALES_RETURN_UPDATE    = "sales_return_update";
    const SALES_RETURN_TRASH     = "sales_return_trash";
    const SALES_RETURN_RESTORE   = "sales_return_restore";
    const SALES_RETURN_DELETE    = "sales_return_delete";

    const PURCHASE_RETURN_VIEW      = "purchase_return_view";
    const PURCHASE_RETURN_CREATE    = "purchase_return_create";
    const PURCHASE_RETURN_UPDATE    = "purchase_return_update";
    const PURCHASE_RETURN_TRASH     = "purchase_return_trash";
    const PURCHASE_RETURN_RESTORE   = "purchase_return_restore";
    const PURCHASE_RETURN_DELETE    = "purchase_return_delete";

    const PAYMENT_SALES_VIEW      = "payment_sales_view";
    const PAYMENT_SALES_CREATE    = "payment_sales_create";
    const PAYMENT_SALES_UPDATE    = "payment_sales_update";
    const PAYMENT_SALES_TRASH     = "payment_sales_trash";
    const PAYMENT_SALES_RESTORE   = "payment_sales_restore";
    const PAYMENT_SALES_DELETE    = "payment_sales_delete";

    const PAYMENT_PURCHASE_VIEW      = "payment_purchase_view";
    const PAYMENT_PURCHASE_CREATE    = "payment_purchase_create";
    const PAYMENT_PURCHASE_UPDATE    = "payment_purchase_update";
    const PAYMENT_PURCHASE_TRASH     = "payment_purchase_trash";
    const PAYMENT_PURCHASE_RESTORE   = "payment_purchase_restore";
    const PAYMENT_PURCHASE_DELETE    = "payment_purchase_delete";

    const PAYMENT_RETURN_VIEW      = "payment_return_view";
    const PAYMENT_RETURN_CREATE    = "payment_return_create";
    const PAYMENT_RETURN_UPDATE    = "payment_return_update";
    const PAYMENT_RETURN_TRASH     = "payment_return_trash";
    const PAYMENT_RETURN_RESTORE   = "payment_return_restore";
    const PAYMENT_RETURN_DELETE    = "payment_return_delete";

    const CUSTOMER_VIEW      = "customer_view";
    const CUSTOMER_CREATE    = "customer_create";
    const CUSTOMER_UPDATE    = "customer_update";
    const CUSTOMER_TRASH     = "customer_trash";
    const CUSTOMER_RESTORE   = "customer_restore";
    const CUSTOMER_DELETE    = "customer_delete";
    const CUSTOMER_IMPORT    = "customer_import";

    const SUPPLIER_VIEW      = "supplier_view";
    const SUPPLIER_CREATE    = "supplier_create";
    const SUPPLIER_UPDATE    = "supplier_update";
    const SUPPLIER_TRASH     = "supplier_trash";
    const SUPPLIER_RESTORE   = "supplier_restore";
    const SUPPLIER_DELETE    = "supplier_delete";
    const SUPPLIER_IMPORT    = "supplier_import";

    const REPORTS_PAYMENT_SALES             = "reports_payment_sales";
    const REPORTS_PAYMENT_PURCHASE          = "reports_payment_purchase";
    const REPORTS_SALES_RETURN_PAYMENT      = "reports_sales_return_payment";
    const REPORTS_PURCHASE_RETURN_PAYMENT   = "reports_purchase_return_payment";
    const REPORTS_SALES                     = "reports_sales";
    const REPORTS_PURCHASE                  = "reports_purchase";
    const REPORTS_CUSTOMER                  = "reports_customer";
    const REPORTS_SUPPLIER                  = "reports_supplier";
    const REPORTS_PROFIT_AND_LOSS           = "reports_profit_and_loss";
    const REPORTS_PRODUCT_QUANTITY_ALERT    = "reports_product_quantity_alert";
    const REPORTS_WAREHOUSE_STOCK_CHART     = "reports_warehouse_stock_chart";

    const SETTINGS_SYSTEM       = "settings_system";
    const SETTINGS_CATEGORY     = "settings_category";
    const SETTINGS_BRAND        = "settings_brand";
    const SETTINGS_CURRENCY     = "settings_currency";
    const SETTINGS_WAREHOUSE    = "settings_warehouse";
    const SETTINGS_UNIT         = "settings_unit";
    const SETTINGS_BACKUP       = "settings_backup";
}

<?php
/**
 * Plugin Name: WooCommerce - Show Orders
 * Plugin URI: https://github.com/alikwelyn/lexart-test/plugins/showorders
 * Description: Display all orders in the current month and total orders.
 * Version: 1.0
 * Author: Alik Welyn
 * Author URI: https://github.com/alikwelyn
 * GitHub Plugin URI: https://github.com/alikwelyn/lexart-test/plugins/showorders
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

function setup_menu(){
    add_menu_page( 'Order Table List', 'Order Table List', 'manage_options', 'lexart-test-total', 'show_all_orders' );
    add_submenu_page( 'lexart-test-total', 'By Month', 'By Month', 'manage_options', 'lexart-test-month', 'show_month_orders' );
}
add_action('admin_menu', 'setup_menu');

function show_all_orders() {

    $show_costumers_orders = wc_get_orders( array(
		'post_type' => wc_get_order_types(),
		'status' => 'all',
        'numberposts' => -1, 
		'orderby' => 'date',
		'order' => 'DESC',
        'limit' => '10',
		'paged' => 1,
		'return' => 'ids'
    ) );
	if (empty($show_costumers_orders)) {
		return array('orders' => array());
	}
    
    ?>
		<div class="wrap">
            <h1 class="wp-heading-inline">All orders</h1>
            <table class="wp-list-table widefat fixed striped table-view-list posts">
                <thead>
                    <tr>
                        <?php foreach ( wc_get_account_orders_columns() as $column_id => $column_name ) : if( $column_name != 'Ações' ){ ?>
                        <th class="woocommerce-orders-table__header column-<?php echo esc_attr( $column_id ); ?>">
                            <span class="nobr"><?php echo esc_html( $column_name ); ?></span>
                        </th>
                        <?php } endforeach; ?>
                    </tr>
                </thead>

                <tbody id="the-list">
                    <?php
                    foreach ( $show_costumers_orders as $customer_order ) {
                        $order      = wc_get_order( $customer_order );
                        $item_count = $order->get_item_count();
                        ?>
                        <tr class="woocommerce-orders-table__row iedit <?php echo esc_attr( $order->get_status() ); ?> hentry">
                            <?php foreach ( wc_get_account_orders_columns() as $column_id => $column_name ) : if( $column_name != 'Ações' ){ ?>
                                <td class="woocommerce-orders-table__cell order_number column-<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>">
                                    <?php if ( has_action( 'woocommerce_my_account_my_orders_column_' . $column_id ) ) : ?>
                                        <?php do_action( 'woocommerce_my_account_my_orders_column_' . $column_id, $order ); ?>

                                    <?php elseif ( 'order-number' === $column_id ) : ?>
                                        <a href="<?php echo esc_url( admin_url( 'post.php?post=' . $order->get_id() ) . '&action=edit' ); ?>">
                                            <?php echo esc_html( _x( '#', 'hash before order number', 'woocommerce' ) . $order->get_order_number() ); ?>
                                        </a>

                                    <?php elseif ( 'order-date' === $column_id ) : ?>
                                        <time datetime="<?php echo esc_attr( $order->get_date_created()->date( 'c' ) ); ?>"><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></time>

                                    <?php elseif ( 'order-status' === $column_id ) : ?>
                                        <mark class="order-status status-pending"><span><?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?></span></mark>

                                    <?php elseif ( 'order-total' === $column_id ) : ?>
                                        <?php
                                        echo wp_kses_post( sprintf( _n( '%1$s for %2$s item', '%1$s for %2$s items', $item_count, 'woocommerce' ), $order->get_formatted_order_total(), $item_count ) );
                                        ?>
                                    <?php endif; ?>
                                </td>
                            <?php } endforeach; ?>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>

                <tfoot>
                    <tr>
                        <?php foreach ( wc_get_account_orders_columns() as $column_id => $column_name ) : if( $column_name != 'Ações' ){ ?>
                        <th class="woocommerce-orders-table__footer column-<?php echo esc_attr( $column_id ); ?>">
                            <span class="nobr"><?php echo esc_html( $column_name ); ?></span>
                        </th>
                        <?php } endforeach; ?>
                    </tr>
                </tfoot>
            </table>
		</div>
        <?php
    } 


function show_month_orders() {

    $current_month = date('m');
    $current_year = date('Y');
        
    $show_costumers_orders = wc_get_orders( array(
		'post_type' => wc_get_order_types(),
		'status' => 'all',
        'numberposts' => -1, 
		'orderby' => 'date',
		'order' => 'DESC',
        'limit' => '10',
		'paged' => 1,
        'monthnum' => $current_month,
        'year'     => $current_year,
		'return' => 'ids'
    ) );
	if (empty($show_costumers_orders)) {
		return array('orders' => array());
	}
    
    ?>
		<div class="wrap">
            <h1 class="wp-heading-inline">Orders by month - <strong><?php echo date("m/Y") ?></strong></h1>
            <table class="wp-list-table widefat fixed striped table-view-list posts">
                <thead>
                    <tr>
                        <?php foreach ( wc_get_account_orders_columns() as $column_id => $column_name ) : if( $column_name != 'Ações' ){ ?>
                        <th class="woocommerce-orders-table__header column-<?php echo esc_attr( $column_id ); ?>">
                            <span class="nobr"><?php echo esc_html( $column_name ); ?></span>
                        </th>
                        <?php } endforeach; ?>
                    </tr>
                </thead>

                <tbody id="the-list">
                    <?php
                    foreach ( $show_costumers_orders as $customer_order ) {
                        $order      = wc_get_order( $customer_order );
                        $item_count = $order->get_item_count();
                        ?>
                        <tr class="woocommerce-orders-table__row iedit <?php echo esc_attr( $order->get_status() ); ?> hentry">
                            <?php foreach ( wc_get_account_orders_columns() as $column_id => $column_name ) : if( $column_name != 'Ações' ){ ?>
                                <td class="woocommerce-orders-table__cell order_number column-<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>">
                                    <?php if ( has_action( 'woocommerce_my_account_my_orders_column_' . $column_id ) ) : ?>
                                        <?php do_action( 'woocommerce_my_account_my_orders_column_' . $column_id, $order ); ?>

                                    <?php elseif ( 'order-number' === $column_id ) : ?>
                                        <a href="<?php echo esc_url( admin_url( 'post.php?post=' . $order->get_id() ) . '&action=edit' ); ?>">
                                            <?php echo esc_html( _x( '#', 'hash before order number', 'woocommerce' ) . $order->get_order_number() ); ?>
                                        </a>

                                    <?php elseif ( 'order-date' === $column_id ) : ?>
                                        <time datetime="<?php echo esc_attr( $order->get_date_created()->date( 'c' ) ); ?>"><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></time>

                                    <?php elseif ( 'order-status' === $column_id ) : ?>
                                        <mark class="order-status status-pending"><span><?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?></span></mark>

                                    <?php elseif ( 'order-total' === $column_id ) : ?>
                                        <?php
                                        echo wp_kses_post( sprintf( _n( '%1$s for %2$s item', '%1$s for %2$s items', $item_count, 'woocommerce' ), $order->get_formatted_order_total(), $item_count ) );
                                        ?>
                                    <?php endif; ?>
                                </td>
                            <?php } endforeach; ?>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>

                <tfoot>
                    <tr>
                        <?php foreach ( wc_get_account_orders_columns() as $column_id => $column_name ) : if( $column_name != 'Ações' ){ ?>
                        <th class="woocommerce-orders-table__footer column-<?php echo esc_attr( $column_id ); ?>">
                            <span class="nobr"><?php echo esc_html( $column_name ); ?></span>
                        </th>
                        <?php } endforeach; ?>
                    </tr>
                </tfoot>
            </table>
		</div>
        <?php
    } 
?>
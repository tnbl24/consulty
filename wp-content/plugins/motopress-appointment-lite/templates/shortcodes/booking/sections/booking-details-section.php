<div class="mpa-booking-details mpa-hide">
    <div class="mpa-cart">
        <div class="mpa-cart-items">
            <div class="mpa-cart-item">
                <div class="item-header">
                    <div class="cell cell-service">
                        <span class="cell-value mpa-service-name"></span>
                    </div>
                    <div class="cell cell-date">
                        <div class="cell-value">
                            <span class="mpa-reservation-date"></span>,
                            <span class="mpa-reservation-time"></span>
                        </div>
                    </div>
                </div>
                <div class="item-footer">
                    <div class="cell cell-actions">
                        <?php esc_html_e( 'Add to your calendar: ', 'motopress-appointment' ); ?>
                        <a href="#" target="_blank" class="mpa-add-to-calendar-link mpa-add-to-calendar-link--google"
                            title="<?php esc_html_e( 'Google', 'motopress-appointment' ); ?>"><span class="mpa-add-to-calendar-link-text"><?php esc_html_e( 'Google', 'motopress-appointment' ); ?></span></a>,
                        <a href="#" target="_self" class="mpa-add-to-calendar-link mpa-add-to-calendar-link--apple"
                            download="cal.ics" title="<?php esc_html_e( 'Apple', 'motopress-appointment' ); ?>"><span class="mpa-add-to-calendar-link-text"><?php esc_html_e( 'Apple', 'motopress-appointment' ); ?></span></a>,
                        <a href="#" target="_self" class="mpa-add-to-calendar-link mpa-add-to-calendar-link--outlook"
                            download="cal.ics" title="<?php esc_html_e( 'Outlook', 'motopress-appointment' ); ?>"><span class="mpa-add-to-calendar-link-text"><?php esc_html_e( 'Outlook', 'motopress-appointment' ); ?></span></a>,
                        <a href="#" target="_blank" class="mpa-add-to-calendar-link mpa-add-to-calendar-link--yahoo"
                            title="<?php esc_html_e( 'Yahoo!', 'motopress-appointment' ); ?>"><span class="mpa-add-to-calendar-link-text"><?php esc_html_e( 'Yahoo!', 'motopress-appointment' ); ?></span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
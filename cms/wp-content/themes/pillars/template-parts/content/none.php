					<div class="block wp-block">

						<h3>Ничего не найдено</h3>

						<?php if (is_home() && current_user_can('publish_posts')) : ?>

							<p><?php printf(__('Готовы опубликовать свой первый пост? <a href="%1$s">Создать пост</a>.', 'ledmebel'), esc_url(admin_url('post-new.php'))); ?></p>

						<?php elseif (is_search()) : ?>

							<p><?php _e('Извините, но ничего не соответствует вашим условиям поиска. Пожалуйста, попытайтесь снова с другими ключевыми словами.', 'ledmebel'); ?></p>
							<?php get_search_form(); ?>

						<?php else : ?>

							<p><?php _e('Кажется, мы не можем найти то, что вы ищете. Возможно, поиск может помочь.', 'ledmebel'); ?></p>
							<?php get_search_form(); ?>

						<?php endif; ?>
						<!-- .page-content -->
						<!-- .no-results -->

					</div>
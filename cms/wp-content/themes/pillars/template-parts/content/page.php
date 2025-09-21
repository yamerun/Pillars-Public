	<section class="page" id="post-<?php the_ID(); ?>">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="block">
						<h1><?php the_title(); ?></h1>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-12">
					<div class="block wp-block">
						<?php the_content(); ?>
					</div>
				</div>
			</div>
		</div>
	</section>
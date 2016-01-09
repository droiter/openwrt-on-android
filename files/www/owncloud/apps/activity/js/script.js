$(function(){
	var OCActivity={};

	OCActivity.Filter = {
		filter: undefined,
		currentPage: 0,
		navigation: $('#app-navigation'),

		setFilter: function (filter) {
			if (filter === this.filter) {
				return;
			}

			this.navigation.find('a[data-navigation=' + this.filter + ']').removeClass('active');
			this.currentPage = 0;

			this.filter = filter;
			OC.Util.History.pushState('filter=' + filter);

			OCActivity.InfinitScrolling.container.animate({ scrollTop: 0 }, 'slow');
			OCActivity.InfinitScrolling.container.children().remove();
			$('#no_activities').addClass('hidden');
			$('#no_more_activities').addClass('hidden');
			$('#loading_activities').removeClass('hidden');
			OCActivity.InfinitScrolling.ignoreScroll = false;

			this.navigation.find('a[data-navigation=' + filter + ']').addClass('active');

			OCActivity.InfinitScrolling.prefill();
		}
	};

	OCActivity.InfinitScrolling = {
		ignoreScroll: false,
		container: $('#container'),
		content: $('#app-content'),

		prefill: function () {
			if (this.content.scrollTop() + this.content.height() > this.container.height() - 100) {
				OCActivity.Filter.currentPage++;

				$.get(
					OC.filePath('activity', 'ajax', 'fetch.php'),
					'filter=' + OCActivity.Filter.filter + '&page=' + OCActivity.Filter.currentPage,
					function (data) {
						if (data.trim().length) {
							OCActivity.InfinitScrolling.appendContent(data);

							// Continue prefill
							OCActivity.InfinitScrolling.prefill();
						} else if (OCActivity.Filter.currentPage == 1) {
							// First page is empty - No activities :(
							$('#no_activities').removeClass('hidden');
							$('#loading_activities').addClass('hidden');
						} else {
							// Page is empty - No more activities :(
							$('#no_more_activities').removeClass('hidden');
							$('#loading_activities').addClass('hidden');
						}
					}
				);
			}
		},

		onScroll: function () {
			if (!OCActivity.InfinitScrolling.ignoreScroll && OCActivity.InfinitScrolling.content.scrollTop() +
			 OCActivity.InfinitScrolling.content.height() > OCActivity.InfinitScrolling.container.height() - 100) {
				OCActivity.Filter.currentPage++;

				OCActivity.InfinitScrolling.ignoreScroll = true;
				$.get(
					OC.filePath('activity', 'ajax', 'fetch.php'),
					'filter=' + OCActivity.Filter.filter + '&page=' + OCActivity.Filter.currentPage,
					function (data) {
						OCActivity.InfinitScrolling.appendContent(data);
						OCActivity.InfinitScrolling.ignoreScroll = false;

						if (!data.trim().length) {
							// Page is empty - No more activities :(
							$('#no_more_activities').removeClass('hidden');
							$('#loading_activities').addClass('hidden');
							OCActivity.InfinitScrolling.ignoreScroll = true;
						}
					}
				);
			}
		},

		appendContent: function (content) {
			var firstNewGroup = $(content).first(),
				lastGroup = this.container.children().last();

			// Is the first new container the same as the last one?
			if (lastGroup && lastGroup.data('date') === firstNewGroup.data('date')) {
				var appendedBoxes = firstNewGroup.find('.box'),
					lastBoxContainer = lastGroup.find('.boxcontainer');

				// Move content into the last box
				OCActivity.InfinitScrolling.processElements(appendedBoxes);
				lastBoxContainer.append(appendedBoxes);

				// Remove the first box, so it's not duplicated
				content = $(content).slice(1);
			} else {
				content = $(content);
			}

			OCActivity.InfinitScrolling.processElements(content);
			this.container.append(content);
		},

		processElements: function (parentElement) {
			$(parentElement).find('.avatar').each(function() {
				var element = $(this);
				element.avatar(element.data('user'), 28);
			});

			$(parentElement).find('.tooltip').tipsy({
				gravity:	's',
				fade:		true
			});
		}
	};

	OCActivity.Filter.setFilter(OCActivity.InfinitScrolling.container.attr('data-activity-filter'));
	OCActivity.InfinitScrolling.content.on('scroll', OCActivity.InfinitScrolling.onScroll);

	OCActivity.Filter.navigation.find('a[data-navigation]').on('click', function (event) {
		OCActivity.Filter.setFilter($(this).attr('data-navigation'));
		event.preventDefault();
	});

	$('#enable_rss').change(function () {
		if (this.checked) {
			$('#rssurl').removeClass('hidden');
		} else {
			$('#rssurl').addClass('hidden');
		}
		$.post(OC.filePath('activity', 'ajax', 'rssfeed.php'), 'enable=' + this.checked, function(data) {
			$('#rssurl').val(data.data.rsslink);
		});
	});

	$('#rssurl').on('click', function () {
		$('#rssurl').select();
	});
});


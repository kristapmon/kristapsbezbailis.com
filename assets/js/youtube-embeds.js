(function () {
  function loadYouTubeEmbed(embed) {
    if (!embed || embed.dataset.youtubeLoaded) {
      return;
    }

    embed.dataset.youtubeLoaded = '1';

    var videoId = embed.dataset.youtubeId;
    var start = embed.dataset.youtubeStart || '0';
    var autoplay = embed.dataset.youtubeAutoplay || '1';
    var title = embed.dataset.youtubeTitle || 'YouTube video';
    var iframe = document.createElement('iframe');
    var src = 'https://www.youtube-nocookie.com/embed/' + encodeURIComponent(videoId) +
      '?autoplay=' + encodeURIComponent(autoplay) +
      '&start=' + encodeURIComponent(start) +
      '&rel=0&modestbranding=1';

    iframe.setAttribute('src', src);
    iframe.setAttribute('title', title);
    iframe.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share');
    iframe.setAttribute('allowfullscreen', '');

    embed.innerHTML = '';
    embed.appendChild(iframe);
  }

  function closestEmbed(target) {
    if (!target || !target.closest) {
      return null;
    }

    return target.closest('.theme-youtube-embed');
  }

  function warmYouTubeConnections() {
    if (warmYouTubeConnections.done) {
      return;
    }

    warmYouTubeConnections.done = true;

    [
      'https://www.youtube-nocookie.com',
      'https://i.ytimg.com',
      'https://yt3.ggpht.com'
    ].forEach(function (href) {
      var link = document.createElement('link');
      link.rel = 'preconnect';
      link.href = href;
      document.head.appendChild(link);
    });
  }

  document.addEventListener('click', function (event) {
    var embed = closestEmbed(event.target);

    if (embed) {
      loadYouTubeEmbed(embed);
    }
  });

  document.addEventListener('keydown', function (event) {
    if (event.key !== 'Enter' && event.key !== ' ') {
      return;
    }

    var embed = closestEmbed(event.target);

    if (embed) {
      event.preventDefault();
      loadYouTubeEmbed(embed);
    }
  });

  document.addEventListener('mouseover', function (event) {
    if (closestEmbed(event.target)) {
      warmYouTubeConnections();
    }
  });

  document.addEventListener('focusin', function (event) {
    if (closestEmbed(event.target)) {
      warmYouTubeConnections();
    }
  });
})();

/**
 * Timeline Lazy Loading
 * Uses Intersection Observer to load more posts as user scrolls
 */
(function() {
    'use strict';

    // Elements
    const timelineContainer = document.querySelector('.timeline-container');
    const loader = document.getElementById('timeline-loader');
    
    // Exit if no timeline container or loader
    if (!timelineContainer || !loader) {
        return;
    }

    // State
    let currentPage = parseInt(timelineContainer.dataset.page) || 1;
    let maxPages = parseInt(timelineContainer.dataset.maxPages) || 1;
    let isLoading = false;

    // Exit if only one page
    if (maxPages <= 1) {
        loader.classList.add('hidden');
        return;
    }

    /**
     * Load more posts via AJAX
     */
    function loadMorePosts() {
        if (isLoading || currentPage >= maxPages) {
            return;
        }

        isLoading = true;
        currentPage++;

        // Update loader text
        const loaderText = loader.querySelector('.timeline-loader-text');
        if (loaderText) {
            loaderText.textContent = 'Loading more posts...';
        }

        // Make AJAX request
        const formData = new FormData();
        formData.append('action', 'load_more_timeline_posts');
        formData.append('page', currentPage);
        formData.append('nonce', timelineAjax.nonce);

        fetch(timelineAjax.ajaxUrl, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.html) {
                // Append new posts to timeline
                timelineContainer.insertAdjacentHTML('beforeend', data.data.html);
                
                // Update state
                maxPages = data.data.max_pages;
                
                // Check if more posts available
                if (!data.data.has_more) {
                    showNoMorePosts();
                }
            } else {
                showNoMorePosts();
            }
        })
        .catch(error => {
            console.error('Timeline load error:', error);
            if (loaderText) {
                loaderText.textContent = 'Error loading posts. Please refresh.';
            }
        })
        .finally(() => {
            isLoading = false;
        });
    }

    /**
     * Show "no more posts" state
     */
    function showNoMorePosts() {
        loader.classList.add('no-more');
        const loaderText = loader.querySelector('.timeline-loader-text');
        if (loaderText) {
            loaderText.textContent = '';
        }
        // Stop observing
        if (observer) {
            observer.disconnect();
        }
        // Hide loader after a moment
        setTimeout(() => {
            loader.classList.add('hidden');
        }, 500);
    }

    /**
     * Intersection Observer callback
     */
    function handleIntersection(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting && !isLoading) {
                loadMorePosts();
            }
        });
    }

    // Create Intersection Observer
    const observerOptions = {
        root: null, // viewport
        rootMargin: '200px', // Start loading before reaching the loader
        threshold: 0
    };

    const observer = new IntersectionObserver(handleIntersection, observerOptions);
    
    // Start observing the loader element
    observer.observe(loader);

})();

@extends('common.layout')

@section('title', 'Gallery')

@section('content')
    <div class="col-md-12">
        <div class="row text-center gallery-title"><h3></h3></div>
        <div class="d-flex align-items-start gallery">
            <div class="nav flex-column nav-pills me-3 col-md-3" id="v-pills-tab" role="tablist" aria-orientation="vertical"></div>
            <div class="tab-content col-md-7 text-center" id="v-pills-tabContent"></div>
        </div>
    </div>
@endsection

@section('additional-scripts')
    <script>
      $(() => {
        $.ajax({
          url: "{{ route('gallery') }}",
          type: 'GET',
          dataType: 'JSON',
          success: response => {
            const categoryButtons = (response?.data?.records || [])
              .map(record => {
                return createNavButton(record.gallery_id, record.title._content);
              });
            const categoryTargets = (response?.data?.records || [])
              .map(record => {
                return createNavTarget(record.gallery_id, record.title._content);
              });

            $('.nav-pills').empty().append(categoryButtons);
            $('.tab-content').empty().append(categoryTargets);
          }
        });

        $(document).on('click', '.nav-link', function () {
          $('.tab-pane').empty();
          $('.gallery-title h3').text(`${$('.tab-pane.active').data('label')} Photo Gallery`);

          const galleryId = $(this).data('gallery-id');
          $.ajax({
            url: `{{ route('gallery') }}/${galleryId}/photos`,
            type: 'GET',
            dataType: 'JSON',
            success: response => {
              const thumbnails = (response?.data?.records || [])
                .map(({ id, thumbnail_url, title }) => {
                  return createImage(id, thumbnail_url, title);
                });

              const originals = (response?.data?.records || [])
                .map(({ id, original_url, title }) => {
                  return createOriginalImageModal(id, original_url, title);
                });

              $('.tab-pane.active').append(thumbnails);
              $('.gallery').append(originals);
            }
          });
        });

        /**
         * Creates a navigation button
         *
         * @param galleryId
         * @param text
         *
         * @returns {*|jQuery|HTMLElement}
         */
        const createNavButton = (galleryId, text) => {
          return $('<button />', {
            class: 'nav-link',
            id: `v-pills-${galleryId}-tab`,
            type: 'button',
            role: 'tab',
            text,
            'aria-controls': `v-pills-${galleryId}`,
            'data-bs-toggle': 'pill',
            'data-bs-target': `#v-pills-${galleryId}`,
            'data-gallery-id': galleryId,
          });
        }

        /**
         * Creates a navigation button target. This will be displayed to the user
         * once its corresponding navigation button is clicked.
         *
         * @param galleryId
         * @param text
         *
         * @returns {*|jQuery|HTMLElement}
         */
        const createNavTarget = (galleryId, text) => {
          return $('<div />', {
            class: 'tab-pane d-flex justify-content-between',
            id: `v-pills-${galleryId}`,
            role: 'tabpanel',
            'data-label': text,
            'aria-labelledby': `v-pills-${galleryId}-tab`,
          });
        }

        /**
         * Creates an image element out of the given id, src, and text attributes
         *
         * @param id
         * @param src
         * @param text
         *
         * @returns {*|jQuery|HTMLElement}
         */
        const createImage = (id, src, text) => {
          return $('<img />', {
            src,
            class: 'img-responsive ml-auto mr-auto mx-auto',
            'data-toggle': 'modal',
            'data-target': `.flickr-${id}`,
          });
        }

        /**
         * Creates an image modal where we're going to display the full image size
         *
         * @param id
         * @param src
         * @param text
         *
         * @returns {*|jQuery|HTMLElement}
         */
        const createOriginalImageModal = (id, src, text) => {
          const modal = $('<div />', {
            class: `flickr-${id} modal fade`,
            tabindex: -1,
            role: 'dialog'
          });
          const dialog = $('<div />', { class: 'modal-dialog mw-100 modal-dialog-centered' });
          const content = $('<div />', { class: 'modal-content' });
          const img = $('<img />', { src, class: 'img-responsive mx-auto' });

          content.append(img);
          dialog.append(content);
          modal.append(dialog);

          return modal;
        }

      })
    </script>
@endsection

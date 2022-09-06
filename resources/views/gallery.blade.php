@extends('common.layout')

@section('title', 'Gallery')

@section('content')
    <div class="col-md-12">
        <div class="row text-center gallery-title"><h3></h3></div>
        <div class="d-flex align-items-start">
            <div class="nav flex-column nav-pills me-3 col-md-3" id="v-pills-tab" role="tablist" aria-orientation="vertical"></div>
            <div class="tab-content col-md-9 text-center" id="v-pills-tabContent"></div>
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

        $(document).on('click', '.nav-link', () => {
            $('.gallery-title h3').text(`${$('.tab-pane.active').data('label')} Photo Gallery`);
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
            class: 'tab-pane',
            id: `v-pills-${galleryId}`,
            role: 'tabpanel',
            'data-label': text,
            'aria-labelledby': `v-pills-${galleryId}-tab`,
          });
        }

      })
    </script>
@endsection

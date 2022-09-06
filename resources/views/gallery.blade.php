@extends('common.layout')

@section('title', 'Gallery')

@section('content')
    <template class="modal-template">
        <div class="flickr-${id} modal fade" tabindex="-1" role="dialog" data-gallery-id="${galleryId}" data-photo-id="${id}">
            <div class="modal-dialog mw-100 modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close float-right" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-horizontal">
                                <div class="img-square-wrapper">
                                    <img class="img-responsive mx-auto" src="${src}">
                                </div>
                                <div class="card-body">
                                    <h4 class="card-title"></h4>
                                    <p class="card-text"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
    <template class="category-button-template">
        <button
            class="nav-link"
            id="v-pills-${galleryId}-tab"
            type="button"
            role="tab"
            aria-controls="v-pills-${galleryId}"
            data-bs-toggle="pill"
            data-bs-target="#v-pills-${galleryId}"
            data-gallery-id="${galleryId}">${text}
        </button>
    </template>
    <template class="nav-target-template">
        <div class="tab-pane d-flex justify-content-between" id="v-pills-${galleryId}" role="tabpanel" data-label="${text}" aria-labelledby="v-pills-${galleryId}-tab">
        </div>
    </template>

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
                  return createOriginalImageModal(galleryId, id, original_url, title);
                });

              $('.tab-pane.active').append(thumbnails);
              $('#v-pills-tabContent').append(originals);
            }
          });
        });

        $(document).on('shown.bs.modal', '.modal', function () {
          const galleryId = $(this).data('gallery-id');
          const photoId = $(this).data('photo-id');

          $.ajax({
            url: `{{ route('gallery') }}/${galleryId}/photos/${photoId}`,
            type: 'GET',
            dataType: 'JSON',
            success: ({ data }) => {
              $(this).find('.card-title').text(data.title._content);
              $(this).find('.card-text').text(data.description._content);
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
          return $(
            interpolate(
              $('template.category-button-template').html(), { galleryId, text }
            )
          );
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
          return $(interpolate($('.nav-target-template').html(), { galleryId, text }));
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
         * @param galleryId
         * @param id
         * @param src
         * @param text
         *
         * @returns {*|jQuery|HTMLElement}
         */
        const createOriginalImageModal = (galleryId, id, src, text) => {
          return $(interpolate($('.modal-template').html(), {
            galleryId,
            id,
            src,
            text
          }));
        }

        /**
         * Given key-value pairs of variables, each "${variable}" will be replaced in
         * the string.
         *
         * @param string
         * @param variables
         * @returns {*}
         */
        const interpolate = (string, variables) => {
          Object.entries(variables).forEach(variable => {
            [key, value] = variable;

            string = string.replaceAll('${'+key+'}', value);
          });

          return string;
        };
      })
    </script>
@endsection

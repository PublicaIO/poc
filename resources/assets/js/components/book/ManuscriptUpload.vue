<template>
    <div class="modal fade" id="manuscript_upload" tabindex="-1" role="dialog">
        <pbl-progress v-if="loading"></pbl-progress>

        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Upload Book File</h4>
                </div>

                <div class="modal-body">
                    <form action="#" method="post" enctype="multipart/form-data" class="form-horizontal">
                        <div class="form-group">
                            <label for="title" class="col-md-4 control-label">Book File</label>
                            <div class="col-md-6">
                                <input type="file" name="passport" @change="processFileField($event, 'book_file')">
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" @click.prevent="submit">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
export default {
    props: [
        'book_id'
    ],

    data() {
        return {
            formData: new FormData(),
            loading: null,
        }
    },

    methods: {
        submit() {
            this.loading = true;

            this.formData.set('id', this.book_id);

            axios
                .post('/book/upload', this.formData)
                .then((response) => {
                    if (response.data.success) {
                        this.loading = false;
                        $('#manuscript_upload').modal('hide');
                    }
                })
                .catch((error) => {
                    this.loading = false;
                    this.logError('ManuscriptUpload::submit', error);
                });
        },

        processFileField(event, fieldName) {
            this.formData.set(fieldName, event.target.files[0], event.target.files[0].name);
        }
    }
}
</script>

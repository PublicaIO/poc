<template>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <figure class="figure">
                            <img :src="avatar" :alt="user.name" class="img-thumbnail img-circle">
                        </figure>
                        <h2>{{ user.name }}</h2>
                    </div>

                    <div class="panel-body">
                        <pbl-user-settings
                            :user="user"
                        >
                        </pbl-user-settings>
                    </div>


                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: [
        'user',
        'avatar'
    ]
}
</script>

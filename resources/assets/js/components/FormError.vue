<template>
    <span class="alert danger" role="alert">
        <slot></slot>
    </span>
</template>

<script>

export default {
    props: ['errors']
}
</script>

<template>
    <div>
        <button @click="onClickButton" class="m-4 py-2 px-2 bg-blue-400 rounded-md shadow-xl">Click Me</button>
    </div>
</template>

<script>
import { reactive, toRefs } from 'vue'
export default {
    name: "TestChild",
    emits: ['hello'],
    setup (props,context) {
        function onClickButton (event) {
        console.log("Emit..")
        context.emit('hello', 'Hello World');
        }
    
        return {
            onClickButton
        }
    }
}
</script>

<style lang="scss" scoped>

</style>
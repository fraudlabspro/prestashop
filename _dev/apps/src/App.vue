<template>
  <div id="app">
    <fraudlabspro />
  </div>
</template>

<script>
import FraudLabsPro from "@/components/fraudlabspro.vue";

export default {
  name: 'App',
  components: {
    FraudLabsPro
  },
  setup() {
    console.log('FraudLabs Pro initialized');
  }
}
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}
</style>

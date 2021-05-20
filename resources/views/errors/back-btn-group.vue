<template>
  <div>
    <button size="large" type="text" @click="backHome">Back to Home Page</button>
    <button size="large" type="text" @click="backPrev">Back to preview page({{ second }}s)</button>
  </div>
</template>

<script>
export default {
  name: 'backBtnGroup',
  data () {
    return {
      second: 5,
      timer: null
    }
  },
  methods: {
    backHome () {
      this.$router.push('/')
    },
    backPrev () {
      this.$router.go(-1)
    }
  },
  mounted () {
    this.timer = setInterval(() => {
      if (this.second === 0) this.backPrev()
      else this.second--
    }, 1000)
  },
  beforeDestroy () {
    clearInterval(this.timer)
  }
}
</script>
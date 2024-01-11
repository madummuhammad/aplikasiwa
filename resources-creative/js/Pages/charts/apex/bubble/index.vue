<script>
import { Link, Head } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";

import { simpleBubbleChart, bubble3DChart } from "./data";

/**
 * Bubble Charts component
 */
export default {
  components: {
    Layout,
    PageHeader,
    Link, Head
  },
  data() {
    return {
      simpleBubbleChart: simpleBubbleChart,
      bubble3DChart: bubble3DChart,
    };
  },
};
</script>

<template>
  <Layout>

    <Head title="Boxplot Charts" />
    <PageHeader title="Boxplot Charts" pageTitle="Apexcharts" />
    <BRow>
      <BCol lg="6">
        <BCard no-body>
          <BCardHeader class="justify-content-between d-flex align-items-center">
            <BCardTitle>Basic Bubble Chart</BCardTitle>
          </BCardHeader>
          <BCardBody>
            <apexchart class="apex-charts" height="350" dir="ltr" :series="simpleBubbleChart.series" :options="simpleBubbleChart.chartOptions"></apexchart>
          </BCardBody>
        </BCard>
      </BCol>
      <BCol lg="6">
        <BCard no-body>
          <BCardHeader class="justify-content-between d-flex align-items-center">
            <BCardTitle>3D Bubble Chart</BCardTitle>
          </BCardHeader>
          <BCardBody>
            <apexchart class="apex-charts" height="350" dir="ltr" :series="bubble3DChart.series" :options="bubble3DChart.chartOptions"></apexchart>
          </BCardBody>
        </BCard>
      </BCol>
    </BRow>
  </Layout>
</template>

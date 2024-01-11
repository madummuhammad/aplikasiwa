<script>
import { Link, Head } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";

import { basicPolarareaChart, polarAreaMonochromeChart } from "./data";

/**
 * Polararea chart component
 */
export default {
  components: {
    Layout,
    PageHeader,
    Link, Head
  },
  data() {
    return {
      basicPolarareaChart: basicPolarareaChart,
      polarAreaMonochromeChart: polarAreaMonochromeChart,
    };
  },
};
</script>

<template>
  <Layout>
    <Head title="Polararea Charts" />
    <PageHeader title="Polararea Charts" pageTitle="Apexcharts" />
    <BRow>
      <BCol lg="6">
        <BCard no-body>
          <BCardHeader class="justify-content-between d-flex align-items-center">
            <BCardTitle>Basic-Polar Area Chart</BCardTitle>
          </BCardHeader>
          <BCardBody>
            <apexchart class="apex-charts" height="330" dir="ltr" :series="basicPolarareaChart.series"
              :options="basicPolarareaChart.chartOptions"></apexchart>
          </BCardBody>
        </BCard>
      </BCol>
      <BCol lg="6">
        <BCard no-body>
          <BCardHeader class="justify-content-between d-flex align-items-center">
            <BCardTitle>Polar-Area Monochrome</BCardTitle>
          </BCardHeader>
          <BCardBody>
            <apexchart class="apex-charts" height="330" dir="ltr" :series="polarAreaMonochromeChart.series"
              :options="polarAreaMonochromeChart.chartOptions"></apexchart>
          </BCardBody>
        </BCard>
      </BCol>
    </BRow>
  </Layout>
</template>

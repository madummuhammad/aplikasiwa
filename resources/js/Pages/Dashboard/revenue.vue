<script>
import {
  CountTo
} from "vue3-count-to";

import getChartColorsArray from "@/common/getChartColorsArray";
import { onMounted, onBeforeMount } from 'vue';
import { ref, watch } from "vue";
import { Global } from '../../global'
export default {
  setup() {
    let series=ref([{
        name: "Orders",
        type: "area",
        data: [],
      },
      {
        name: "Earnings",
        type: "bar",
        data: [],
      }
      ])
      let chartOptions=ref({
        chart: {
          height: 370,
          type: "line",
          toolbar: {
            show: false,
          },
        },
        stroke: {
          curve: "straight",
          dashArray: [0, 0, 8],
          width: [2, 0, 2.2],
        },
        fill: {
          opacity: [0.1, 0.9, 1],
        },
        markers: {
          size: [0, 0, 0],
          strokeWidth: 2,
          hover: {
            size: 4,
          },
        },
        xaxis: {
          categories: [
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "May",
            "Jun",
            "Jul",
            "Aug",
            "Sep",
            "Oct",
            "Nov",
            "Dec",
          ],
          axisTicks: {
            show: false,
          },
          axisBorder: {
            show: false,
          },
        },
        grid: {
          show: true,
          xaxis: {
            lines: {
              show: true,
            },
          },
          yaxis: {
            lines: {
              show: false,
            },
          },
          padding: {
            top: 0,
            right: -2,
            bottom: 15,
            left: 10,
          },
        },
        legend: {
          show: true,
          horizontalAlign: "center",
          offsetX: 0,
          offsetY: -5,
          markers: {
            width: 9,
            height: 9,
            radius: 6,
          },
          itemMargin: {
            horizontal: 10,
            vertical: 0,
          },
        },
        plotOptions: {
          bar: {
            columnWidth: "30%",
            barHeight: "70%",
          },
        },
        colors: getChartColorsArray('["--vz-light", "--vz-primary", "--vz-secondary"]'),
        tooltip: {
          shared: true,
          y: [{
            formatter: function (y) {
              if (typeof y !== "undefined") {
                return y.toFixed(0);
              }
              return y;
            },
          },
          {
            formatter: function (y) {
              if (typeof y !== "undefined") {
                return "$" + y.toFixed(2) + "k";
              }
              return y;
            },
          },
          {
            formatter: function (y) {
              if (typeof y !== "undefined") {
                return y.toFixed(0) + " Sales";
              }
              return y;
            },
          },
          ],
        },
      })
    let dashboardData = ref({})
    const global = Global();
    var statisticLoading = ref(true)
    let orders=ref([]);
    let earnings=ref([]);
    const getStatistic = async () => {
      try {
        const response = await axios.get('/api/dashboard/statistic');
        const data = response.data;
        dashboardData.value = data.data;
        series.value[0].data=data.data.diagram.orders
        series.value[1].data=data.data.diagram.earnings
        console.log('statistic', data.data)
        console.log('orders', orders.value)
        statisticLoading.value = false
        console.log(statisticLoading.value)
      } catch (error) {
        console.error('error', error);
      }

    }

    onMounted(() => {
      getStatistic();
    })

    return {
      getStatistic,
      dashboardData,
      statisticLoading,
      orders,
      earnings,
      series,
      chartOptions,
      global
    }
  },
  components: {
    CountTo,
  },
  data() {
    console.log('this',this.orders)
  },
};
</script>

<template>
  <BCard no-body>
    <!-- <BCardHeader class="border-0 align-items-center d-flex">
      <BCardTitle class="mb-0 flex-grow-1">Transaksi</BCardTitle>
      <div class="hstack gap-1">
        <BButton type="button" variant="soft-secondary" size="sm">
          ALL
        </BButton>
        <BButton type="button" variant="soft-secondary" size="sm">
          1M
        </BButton>
        <BButton type="button" variant="soft-secondary" size="sm">
          6M
        </BButton>
        <BButton type="button" variant="soft-primary" size="sm">
          1Y
        </BButton>
      </div>
    </BCardHeader> -->

    <BCardHeader class="p-0 border-0 bg-light-subtle">
      <BRow class="g-0 text-center" v-if="statisticLoading == false">
        <BCol cols="6" sm="3">
          <div class="p-3 border border-dashed border-start-0">
            <h5 class="mb-1">
              {{ dashboardData.statistic.order }}
            </h5>
            <p class="text-muted mb-0">Total Orders</p>
          </div>
        </BCol>
        <BCol cols="6" sm="3">
          <div class="p-3 border border-dashed border-start-0">
            <h5 class="mb-1">
              {{ global.formatNumber(dashboardData.statistic.paid) }}
            </h5>
            <p class="text-muted mb-0">Total Paid</p>
          </div>
        </BCol>
        <BCol cols="6" sm="3">
          <div class="p-3 border border-dashed border-start-0">
            <h5 class="mb-1">
              {{ dashboardData.statistic.rasio}} %
            </h5>
            <p class="text-muted mb-0">Paid Ratio</p>
          </div>
        </BCol>
        <BCol cols="6" sm="3">
          <div class="p-3 border border-dashed border-start-0 border-end-0">
            <h5 class="mb-1 text-primary">
              {{ dashboardData.statistic.unpaid }}%
            </h5>
            <p class="text-muted mb-0">Unpaid Orders</p>
          </div>
        </BCol>
      </BRow>
    </BCardHeader>

    <BCardBody class="p-0 pb-2">
      <div class="w-100">
        <apexchart class="apex-charts" height="370" type="line" dir="ltr" :series="series" :options="chartOptions">
        </apexchart>
      </div>
    </BCardBody>
  </BCard>
</template>
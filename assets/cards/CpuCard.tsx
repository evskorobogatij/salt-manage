import React, {useEffect, useState} from "react";
// import { MouseEvent } from 'react';
import Card from "@material-ui/core/Card";
import CardHeader from "@material-ui/core/CardHeader";
import CardContent from '@material-ui/core/CardContent';
import Grid from "@material-ui/core/Grid";
import DataChart from "@components/DataChart";
import {ChartProps, ChartDataItem} from "@components/DataChart/DataChart";
import {CPU_MODEL} from "@add_types/filters/minion_filters";
import useSWR from "swr";
import {fetcher} from "@pages/fetcher";

export default function CpuCard(){

    const {data} = useSWR<ChartDataItem[]>('/api/cpu_model/cpu_static',fetcher)

    return (
        <>
            <Grid item lg={4} md={6} sm={12}>
                <Card>
                    <CardHeader title={"CPU"} subheader={"Зоопарк процессоров"}/>
                    <CardContent>
                        <DataChart data={data!=undefined ? data : []} height={170} legendWidth={240} field={CPU_MODEL} />
                    </CardContent>
                </Card>
            </Grid>
        </>
    )
}
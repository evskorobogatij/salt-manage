import React, {useEffect, useState} from "react";

import DataChart from './../components/DataChart'
import {ChartDataItem} from "../components/DataChart/DataChart";
import Grid from "@material-ui/core/Grid";
import Card from "@material-ui/core/Card/Card";
import CardHeader from "@material-ui/core/CardHeader";
import CardContent from "@material-ui/core/CardContent";

export default function TypeCard(){

    const [data, setData] = useState<ChartDataItem[]>([])

    useEffect(()=>{
        fetch("/api/type/type_statistic").then(response=>response.json()).then(result=>setData(result))
    },[])

    return (
        <>
            <Grid item>
                <Card>
                    <CardHeader title={"Типы"} subheader={"Типы зарегистрированных миньонов"}/>
                    <CardContent>

                        <DataChart data={data} height={180} />

                    </CardContent>
                </Card>
            </Grid>
        </>
    )
}
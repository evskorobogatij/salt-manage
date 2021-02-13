import React from "react";

import Grid from "@material-ui/core/Grid";
import {createStyles, makeStyles} from "@material-ui/core/styles";
import InfoCard from "../cards/InfoCard";

function Dashboard() {

    const useStyles = makeStyles(() =>
        createStyles({
            root : {
                flexGrow: 1
            },
            card : {
                margin: 12
            }
        }))

    const classes = useStyles();

    return (
        <>
            <Grid container /*direction={"row"}*/ spacing={4} className={classes.root}>

                <Grid item xs={12}>
                    <h1>Панель управления</h1>
                </Grid>

                <Grid item xs={12} >
                    <Grid container spacing={2}>

                        <InfoCard />

                    </Grid>
                </Grid>

            </Grid>

        </>
    )
}

export default Dashboard
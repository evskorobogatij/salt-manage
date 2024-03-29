import React from "react";
import Grid from "@material-ui/core/Grid";

import {
    CompareType,
    CPU_MODEL,
    CREATE_AT,
    DEPARTMENT,
    FIO_USER,
    IP,
    MAC,
    MANUFACTURER,
    NODE_NAME,
    OS,
    OS_RELEASE,
    ROOM,
    SERIALNUMBER,
    TYPE,
    UPDATED_AT,
    USER_PHONE
} from "@add_types/filters/minion_filters";

import {DateSelect} from "@pages/Minions/DateSelect";
import {FilteredElement} from "@pages/Minions/filteredElement";
import {useStyles} from "@pages/Minions/styles";
import FilteredSelectableElement from "@pages/Minions/filteredSelectableElement";

export function FilterBlock() {

    const classes  = useStyles()
    return (
        <div className={classes.popper_in}>
            <Grid container spacing={2}>
                <Grid item xs={6}>
                    <FilteredElement title={"Имя компьютера"} field={NODE_NAME}/>
                </Grid>
                <Grid item xs={6}>
                    <FilteredElement title={"Серийный номер"} field={SERIALNUMBER}/>
                </Grid>
                <Grid item xs={6} >
                    <FilteredElement title={"IP-адрес"} field={IP}/>
                </Grid>
                <Grid item xs={6}>
                    <FilteredElement title={"MAC адрес"} field={MAC}/>
                </Grid>
                <Grid item xs={4}>
                    <FilteredElement title={"ФИО ответственного"} field={FIO_USER}/>
                </Grid>
                <Grid item xs={4}>
                    <FilteredElement title={"Телефон"} field={USER_PHONE}/>
                </Grid>
                <Grid item xs={4}>
                    <FilteredElement title={"Кабинет"} field={ROOM}/>
                </Grid>
                <Grid item xs={6}>
                    <FilteredSelectableElement title={"Подразделение"} field={DEPARTMENT} />
                </Grid>
                <Grid item xs={6}>
                    <FilteredSelectableElement title={"Тип"} field={TYPE}/>
                </Grid>
                <Grid item xs={4}>
                    <FilteredSelectableElement title={"ОС"} field={OS}/>
                </Grid>
                <Grid item xs={4}>
                    <FilteredElement title={"Версия ОС"} field={OS_RELEASE} compare={CompareType.EQUAL}/>
                </Grid>
                <Grid item xs={4}>
                    <FilteredSelectableElement title={"Производитель"} field={MANUFACTURER} />
                </Grid>
                <Grid item xs={12}>
                    <FilteredSelectableElement title={"ЦПУ"} field={CPU_MODEL}/>
                </Grid>
                <Grid item xs={12}>
                    <DateSelect title={"Дата регистрации миньона"} field={CREATE_AT} /*compare={CompareType.MORE_AND_EQUAL} date={new Date()} */ />
                </Grid>
                <Grid item xs={12}>
                    <DateSelect title={"Дата обновления миньона"} field={UPDATED_AT}/>
                </Grid>
            </Grid>
        </div>
    )
}
import React from "react";
import useStyles from "./styles";
import CardHeader from "@material-ui/core/CardHeader";
import CardContent from "@material-ui/core/CardContent";
import MinionParamItem from "./MinionParamItem";
import Card from "@material-ui/core/Card/Card";
import ComputerIcon from '@material-ui/icons/Computer';
import Divider from "@material-ui/core/Divider";

interface IMinionDetail {
    node_name? : string
    serialnumber? : string
    biosversion? : string
    biosreleasedate? : Date
    manufacturer? : string
    cpu_model? : string
    product_name? : string
    saltversion? : string
    os? : string
    osrelease? : string
    created_at? : Date
    updated_at? : Date
}

function MinionMainInfo(detail:IMinionDetail) {
    const classes = useStyles()
    return (
        <>
            <Card >
                <CardHeader
                    title={"Основные сведения"}
                    avatar={<ComputerIcon/>}
                />
                <CardContent>

                    <MinionParamItem title={"Имя компьютера"} value={detail.node_name}/>
                    <MinionParamItem title={"Серийный номер"} value={detail.serialnumber}/>
                    <MinionParamItem title={"Производитель"} value={detail.manufacturer}/>
                    <MinionParamItem title={"Модель"} value={detail?.product_name}/>
                    <MinionParamItem title={"CPU"} value={detail?.cpu_model}/>
                    <MinionParamItem title={"ОС"} value={detail?.os}/>
                    <MinionParamItem title={"Версия ОС"} value={detail?.osrelease}/>
                    <MinionParamItem title={"Версия BIOS"} value={detail?.biosversion}/>
                    <MinionParamItem title={"Дата BIOS"} value={detail?.biosreleasedate?.toLocaleDateString()}/>
                    <MinionParamItem title={"salt"} value={detail?.saltversion}/>
                    <Divider variant={"middle"} />
                    <MinionParamItem title={"Создано"} value={detail?.created_at?.toLocaleString()}/>
                    <MinionParamItem title={"Обновлено"} value={detail?.updated_at?.toLocaleString()}/>


                </CardContent>
            </Card>
        </>
    )
}

export default MinionMainInfo
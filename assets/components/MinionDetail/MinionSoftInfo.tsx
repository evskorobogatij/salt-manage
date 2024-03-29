import React, {useEffect, useMemo, useState} from "react";
import {MinionSoft} from "@interfaces/MinionDetailInterfaces";
import {DataGrid, ColDef, RowsProp} from "@material-ui/data-grid";
import CardHeader from "@material-ui/core/CardHeader";
import AppsIcon from '@material-ui/icons/Apps';
import CardContent from "@material-ui/core/CardContent";

import Card from "@material-ui/core/Card/Card";
import numeral from "numeral"
import {renderCellExpand, GridCellExpand} from "@components/addons/GridCellExpand";
import CustomGridPagination from "@components/addons/CustomGridPagination";
import {RU_LOCALE_TEXT} from "@components/addons/grid_ru";
import {NavLink} from "react-router-dom";
import {createStyles, makeStyles, Theme} from "@material-ui/core";
// import InputLabel from "@material-ui/core/InputLabel/InputLabel";
import InputLabel from "@material-ui/core/InputLabel";
import OutlinedInput from "@material-ui/core/OutlinedInput";
import InputAdornment from "@material-ui/core/InputAdornment";
import IconButton from "@material-ui/core/IconButton";
import FormControl from "@material-ui/core/FormControl";
import ClearIcon from "@material-ui/icons/Clear";
import Grid from "@material-ui/core/Grid";


interface ISoftList {
    soft? : MinionSoft[]
}

const useStyles = makeStyles((theme: Theme) =>
    createStyles({
        table_cell_link : {
            textDecoration : 'none',
            color : theme.palette.text.primary
        }
    }),
);

function MinionSoftInfo(data:ISoftList){

    const classes = useStyles();

    const [soft, setSoft] = useState<RowsProp>([])
    const [find, setFind ] = useState('')

    const filteredSoft = useMemo(()=>soft.filter((item)=>item.name.toLowerCase().includes(find.toLowerCase())),[soft,find])

    useEffect(()=>{
        if (data?.soft){
            setSoft(data.soft)
        } else {
            setSoft([])
        }

    },[data.soft])

    const columns: ColDef[] = [
        {
            field:'name',
            headerName:'Наименование',
            flex:1,
        //    renderCell: renderCellExpand
            renderCell: params => {
                console.log(params.row.id)
                return  (
                    <NavLink to={`/programms/${params.getValue('soft_id')}`} className={classes.table_cell_link} >
                        {/*{params.value}*/}
                        {/*<renderCellExpand/>*/}
                        <GridCellExpand
                            value={params.value ? params.value.toString() : ''}
                            width={params.colDef.width}
                        />
                    </NavLink>
                )
            }
        },
        {
            field:'size',
            headerName:'Размер',
            // flex:1,
            width: 140,
            type:"number",
            valueFormatter: params => numeral(params.value as number).format('0.00 b')
        },
        {field:'version',headerName:'Версия',type:"string", width:120}
    ]

    return (
        <>
            <Card  >
                <CardHeader
                    title={"Сведения о ПО"}
                    avatar={<AppsIcon/>}
                />
                <CardContent>
                    <Grid item xs={12} style={{paddingBottom: '1rem'}}>
                        <FormControl variant={"outlined"} fullWidth /*className={classes.find} */ >
                            <InputLabel htmlFor={"soft-find"}>Поиск</InputLabel>
                            <OutlinedInput
                                id={"soft-find"}
                                value={find}
                                onChange={(e)=>setFind(e.target.value)}
                                label={'Поиск'}
                                endAdornment={
                                    <InputAdornment position={"end"}>
                                        <IconButton onClick={()=>setFind('')} >
                                            <ClearIcon />
                                        </IconButton>
                                    </InputAdornment>
                                }
                            />
                        </FormControl>
                    </Grid>


                    <div style={{ height: '68vh', width: '100%' }}>
                        <DataGrid
                            columns={columns}
                            rows={filteredSoft}
                            loading={data.soft===undefined}
                            autoPageSize={true}
                            disableSelectionOnClick={true}
                            localeText={RU_LOCALE_TEXT}
                            // pageSize={15}
                            density={"compact"}
                            components={{
                                Pagination : CustomGridPagination
                            }}
                        />
                    </div>
                </CardContent>
            </Card>

        </>
    )
}

export default MinionSoftInfo
import React, {useEffect, useState} from "react";
import Badge from "@material-ui/core/Badge";
import ComputerIcon from '@material-ui/icons/Computer';
import IconButton from "@material-ui/core/IconButton";
import {MinionListItem} from "@interfaces/MinionDetailInterfaces";
import ClickAwayListener from '@material-ui/core/ClickAwayListener';
import Grow from '@material-ui/core/Grow';
import Fade from "@material-ui/core/Fade";
import Paper from '@material-ui/core/Paper';
import Popper from '@material-ui/core/Popper';
import MenuItem from '@material-ui/core/MenuItem';
import MenuList from '@material-ui/core/MenuList';
import {createStyles, makeStyles, Theme} from "@material-ui/core";
import {Link} from "react-router-dom";
import Button from "@material-ui/core/Button";
import useSWR from "swr";
import {fetcher} from "@pages/fetcher";

const useStyles = makeStyles((theme:Theme)=>createStyles({
    poper : {
        zIndex : 9000
    },
    popper: {
        zIndex: 2000,
        '&[x-placement*="bottom"] $arrow': {
            top: 0,
            left: 0,
            marginTop: "-0.71em",
            marginLeft: 4,
            marginRight: 4,
            "&::before": {
                transformOrigin: "0 100%"
            }
        },
        '&[x-placement*="top"] $arrow': {
            bottom: 0,
            left: 0,
            marginBottom: "-0.71em",
            marginLeft: 4,
            marginRight: 4,
            "&::before": {
                transformOrigin: "100% 0"
            }
        },
        '&[x-placement*="right"] $arrow': {
            left: 0,
            marginLeft: "-0.71em",
            height: "1em",
            width: "0.71em",
            marginTop: 4,
            marginBottom: 4,
            "&::before": {
                transformOrigin: "100% 100%"
            }
        },
        '&[x-placement*="left"] $arrow': {
            right: 0,
            marginRight: "-0.71em",
            height: "1em",
            width: "0.71em",
            marginTop: 4,
            marginBottom: 4,
            "&::before": {
                transformOrigin: "0 0"
            }
        }
    },
    arrow: {
        overflow: "hidden",
        position: "absolute",
        width: "1em",
        height: "0.71em" /* = width / sqrt(2) = (length of the hypotenuse) */,
        boxSizing: "border-box",
        color: theme.palette.background.paper,
        "&::before": {
            content: '""',
            margin: "auto",
            display: "block",
            width: "100%",
            height: "100%",
            boxShadow: theme.shadows[1],
            backgroundColor: "currentColor",
            transform: "rotate(45deg)"
        }
    },
    // paper : {
    //     overflowX: "unset",
    //     overflowY: "unset",
    //     "&::before": {
    //         content: '""',
    //         position: "absolute",
    //         marginRight: "-0.71em",
    //         bottom: 0,
    //         right: 0,
    //         width: 10,
    //         height: 10,
    //         backgroundColor: theme.palette.background.paper,
    //         boxShadow: theme.shadows[1],
    //         transform: "translate(-50%, 50%) rotate(135deg)",
    //         clipPath: "polygon(-5px -5px, calc(100% + 5px) -5px, calc(100% + 5px) calc(100% + 5px))",
    //     },
    // },
}))

export function Computer(){

    const classes = useStyles()

    const initialData:MinionListItem[] = []
    const { data:newMinions} = useSWR<MinionListItem[]>('/api/minion/new_minions',fetcher,{refreshInterval:1000*60*15})

    const [open, setOpen] = useState(false)

    const anchorRef = React.useRef<HTMLButtonElement>(null);
    const [arrowRef, setArrowRef] = React.useState<HTMLElement | null>(null);

    const handleOpen = (event: React.MouseEvent<HTMLButtonElement>) =>{
        setOpen(true)
    }

    const handleClose = () => {
        setOpen(false)
    }

    return (
        <>
            <IconButton color={"inherit"} onClick={handleOpen} ref={anchorRef}>
                <Badge badgeContent={newMinions?.length} color={"secondary"}>
                    <ComputerIcon />
                </Badge>
            </IconButton>

            <Popper open={open && (newMinions!=undefined ? newMinions.length>0 : false  ) } anchorEl={anchorRef.current} role={undefined} placement={"bottom"} transition className={classes.popper}
                    modifiers={{
                        preventOverflow: {
                            enabled: true,
                            boundariesElement: "window"
                        },
                        arrow: {
                            enabled: true,
                            element: arrowRef
                        },
                    }} >
                {({ TransitionProps, placement }) => (
               /*     <Grow
                        {...TransitionProps}
                        style={{ transformOrigin: placement === 'bottom' ? 'center top' : 'center bottom' }}
                    > */
                    <Fade {...TransitionProps} timeout={350}>
                        <Paper>
                            <ClickAwayListener onClickAway={handleClose}>
                                <Paper>
                                    <span className={classes.arrow} ref={setArrowRef} />
                                    <MenuList autoFocusItem={open} id="menu-list-grow" /*onKeyDown={handleListKeyDown} */>
                                        {
                                            newMinions?.map((item,index)=><MenuItem key={index} component={Link} to={`/minions/${item.id}`}>{item.node_name}</MenuItem>)
                                        }
                                    </MenuList>
                                </Paper>

                            </ClickAwayListener>
                        </Paper>
                    </Fade>
                  /*  </Grow> */
                )}
            </Popper>

        </>
    )
}